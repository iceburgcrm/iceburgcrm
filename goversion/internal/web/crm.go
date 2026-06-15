package web

import (
	"database/sql"
	"encoding/csv"
	"fmt"
	"html/template"
	"io"
	"math"
	"net/http"
	"path/filepath"
	"strconv"
	"strings"
	"time"

	"iceburgcrm/goversion/internal/db"
)

func (s *Server) can(r *http.Request, moduleID int64, typ string) bool {
	user := s.currentUser(r)
	if user == nil {
		return false
	}
	if user.Role == "Admin" {
		return true
	}
	col := "can_" + typ
	if !db.ValidIdent(col) {
		return false
	}
	var ok int
	_ = s.db.QueryRowContext(r.Context(), `SELECT COUNT(*) FROM ice_permissions WHERE module_id=? AND role_id=? AND `+db.Q(col)+`=1`, moduleID, user.RoleID).Scan(&ok)
	_, _ = s.db.ExecContext(r.Context(), `INSERT INTO ice_logs (user_id,type,module_id,created_at,updated_at) VALUES (?,?,?,?,?)`, user.ID, typ, moduleID, time.Now(), time.Now())
	return ok > 0
}

func (s *Server) download(w http.ResponseWriter, r *http.Request, path string) {
	parts := strings.Split(path, "/")
	if len(parts) < 3 {
		http.NotFound(w, r)
		return
	}
	moduleID := int64From(parts[1])
	exportType := strings.ToLower(parts[2])
	module, err := db.GetModule(r.Context(), s.db, moduleID)
	if err != nil || !s.can(r, moduleID, "export") {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "No Access"})
		return
	}
	fields, _ := db.Fields(r.Context(), s.db, moduleID, "All")
	cols := db.SafeColumnList(fields)
	if len(cols) == 0 {
		writeJSON(w, http.StatusOK, []any{})
		return
	}
	selects := make([]string, len(cols))
	for i, col := range cols {
		selects[i] = db.Q(col)
	}
	input := formMap(r)
	ids := idsFrom(input)
	where := ""
	args := []any{}
	if len(ids) > 0 {
		placeholders := make([]string, len(ids))
		for i, id := range ids {
			placeholders[i] = "?"
			args = append(args, id)
		}
		where = ` WHERE ` + db.Q(module.PrimaryField) + ` IN (` + strings.Join(placeholders, ",") + `)`
	} else {
		limit := intFrom(db.Setting(r.Context(), s.db, "max_export_records"), 1000)
		args = append(args, limit)
	}
	query := `SELECT ` + strings.Join(selects, ",") + ` FROM ` + db.Q(module.Name) + where
	if len(ids) == 0 {
		query += ` LIMIT ?`
	}
	rows, err := s.db.QueryContext(r.Context(), query, args...)
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	defer rows.Close()
	w.Header().Set("Content-Disposition", `attachment; filename="`+module.Name+`.`+exportType+`"`)
	if exportType == "html" {
		w.Header().Set("Content-Type", "text/html; charset=utf-8")
		s.writeHTMLTable(w, cols, rows)
		return
	}
	if exportType == "tsv" {
		w.Header().Set("Content-Type", "text/tab-separated-values")
	} else {
		w.Header().Set("Content-Type", "text/csv")
	}
	writer := csv.NewWriter(w)
	if exportType == "tsv" {
		writer.Comma = '\t'
	}
	_ = writer.Write(cols)
	raw := make([]any, len(cols))
	ptrs := make([]any, len(cols))
	for i := range raw {
		ptrs[i] = &raw[i]
	}
	for rows.Next() {
		if err := rows.Scan(ptrs...); err != nil {
			continue
		}
		record := make([]string, len(cols))
		for i, value := range raw {
			switch v := value.(type) {
			case []byte:
				record[i] = string(v)
			default:
				record[i] = fmt.Sprint(v)
			}
		}
		_ = writer.Write(record)
	}
	writer.Flush()
}

func (s *Server) writeHTMLTable(w http.ResponseWriter, cols []string, rows *sql.Rows) {
	_, _ = w.Write([]byte("<table><thead><tr>"))
	for _, col := range cols {
		_, _ = w.Write([]byte("<th>" + template.HTMLEscapeString(col) + "</th>"))
	}
	_, _ = w.Write([]byte("</tr></thead><tbody>"))
	raw := make([]any, len(cols))
	ptrs := make([]any, len(cols))
	for i := range raw {
		ptrs[i] = &raw[i]
	}
	for rows.Next() {
		if err := rows.Scan(ptrs...); err != nil {
			continue
		}
		_, _ = w.Write([]byte("<tr>"))
		for _, value := range raw {
			cell := fmt.Sprint(value)
			if bytes, ok := value.([]byte); ok {
				cell = string(bytes)
			}
			_, _ = w.Write([]byte("<td>" + template.HTMLEscapeString(cell) + "</td>"))
		}
		_, _ = w.Write([]byte("</tr>"))
	}
	_, _ = w.Write([]byte("</tbody></table>"))
}

func (s *Server) importData(w http.ResponseWriter, r *http.Request) {
	if err := r.ParseMultipartForm(32 << 20); err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	moduleID := int64From(r.FormValue("module_id"))
	if !s.can(r, moduleID, "import") {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "No Access"})
		return
	}
	file, _, err := r.FormFile("input_file")
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "input_file is required"})
		return
	}
	defer file.Close()
	reader := csv.NewReader(file)
	reader.FieldsPerRecord = -1
	rows, err := reader.ReadAll()
	if err != nil && err != io.EOF {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	fields, _ := db.Fields(r.Context(), s.db, moduleID, "All")
	names := db.SafeColumnList(fields)
	firstRowHeader := r.FormValue("first_row_header") == "1" || r.FormValue("first_row_header") == "true"
	start := 0
	if firstRowHeader {
		start = 1
	}
	if r.FormValue("preview") == "1" || r.FormValue("preview") == "true" {
		row := []string{}
		if len(rows) > start {
			row = rows[start]
		}
		writeJSON(w, http.StatusOK, map[string]any{"preview": 1, "fields": names, "row": row})
		return
	}
	module, err := db.GetModule(r.Context(), s.db, moduleID)
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	updated := 0
	for _, row := range rows[start:] {
		input := map[string]any{"module_id": moduleID}
		for i, col := range names {
			if i < len(row) {
				input[fmt.Sprintf("%d__%s", moduleID, col)] = row[i]
			}
		}
		if _, err := s.saveModuleRecord(r, module.ID, input); err == nil {
			updated++
		}
	}
	writeJSON(w, http.StatusOK, map[string]any{"records_updated": updated})
}

func (s *Server) permissions(r *http.Request, moduleID int64) map[string]int {
	out := map[string]int{}
	for _, typ := range []string{"read", "write", "import", "export"} {
		if s.can(r, moduleID, typ) {
			out[typ] = 1
		} else {
			out[typ] = 0
		}
	}
	return out
}

func (s *Server) getRecord(r *http.Request, module *db.Module, id int64) map[string]any {
	if !db.ValidIdent(module.Name) || !db.ValidIdent(module.PrimaryField) {
		return nil
	}
	rows, err := s.db.QueryContext(r.Context(), `SELECT *, `+db.Q(module.PrimaryField)+` AS row_id, `+db.Q(module.PrimaryField)+` AS `+db.Q(module.Name+"_row_id")+` FROM `+db.Q(module.Name)+` WHERE `+db.Q(module.PrimaryField)+`=? LIMIT 1`, id)
	if err != nil {
		return nil
	}
	item, _ := db.RowToMap(rows)
	return item
}

func (s *Server) saveRecord(w http.ResponseWriter, r *http.Request) {
	s.saveRecordInput(w, r, formMap(r))
}

func (s *Server) saveRecordInput(w http.ResponseWriter, r *http.Request, input map[string]any) {
	moduleID := int64From(input["module_id"])
	relationshipID := int64From(input["relationship_id"])
	if moduleID == 0 && relationshipID == 0 {
		writeJSON(w, http.StatusBadRequest, map[string]any{"error": "Missing module_id"})
		return
	}
	if moduleID > 0 && !s.can(r, moduleID, "write") {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "No Access"})
		return
	}
	id, err := s.saveSubmittedRecords(r, moduleID, relationshipID, input)
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	writeJSON(w, http.StatusOK, id)
}

func (s *Server) saveSubmittedRecords(r *http.Request, moduleID, relationshipID int64, input map[string]any) (any, error) {
	selected := selectedModuleRecords(input)
	recordID := int64From(input["record_id"])
	grouped := groupedNewRecords(input)
	if len(grouped) == 0 && moduleID > 0 && relationshipID == 0 {
		id, err := s.saveModuleRecord(r, moduleID, input)
		if err != nil {
			return nil, err
		}
		selected[moduleID] = id
		return id, nil
	}
	for targetModuleID, fields := range grouped {
		if targetModuleID == moduleID && selected[targetModuleID] > 0 {
			continue
		}
		if !s.can(r, targetModuleID, "write") {
			return nil, fmt.Errorf("no access")
		}
		fields["module_id"] = targetModuleID
		fields["from_id"] = input["from_id"]
		fields["from_module"] = input["from_module"]
		if targetModuleID == moduleID {
			fields["record_id"] = recordID
		}
		id, err := s.saveModuleRecord(r, targetModuleID, fields)
		if err != nil {
			return nil, err
		}
		selected[targetModuleID] = id
	}
	if relationshipID > 0 {
		return s.saveRelationshipRecord(r, relationshipID, selected, int64From(input["relationship_record_id"]))
	}
	if moduleID > 0 {
		if id := selected[moduleID]; id > 0 {
			return id, nil
		}
	}
	return nil, fmt.Errorf("no record fields submitted")
}

func (s *Server) saveModuleRecord(r *http.Request, moduleID int64, input map[string]any) (int64, error) {
	module, err := db.GetModule(r.Context(), s.db, moduleID)
	if err != nil {
		return 0, err
	}
	if !db.ValidIdent(module.Name) || !db.ValidIdent(module.PrimaryField) {
		return 0, fmt.Errorf("invalid module metadata")
	}
	fields, err := db.Fields(r.Context(), s.db, moduleID, "All")
	if err != nil {
		return 0, err
	}
	allowed := map[string]db.Field{}
	for _, field := range fields {
		allowed[field.Name] = field
	}
	values := map[string]any{}
	for key, value := range input {
		parts := strings.SplitN(key, "__", 2)
		if len(parts) != 2 {
			continue
		}
		if int64From(parts[0]) != moduleID {
			continue
		}
		field, ok := allowed[parts[1]]
		if !ok || !db.ValidIdent(field.Name) {
			continue
		}
		values[field.Name] = coerceValue(field, value)
	}
	if len(values) == 0 {
		return 0, fmt.Errorf("no record fields submitted")
	}
	recordID := int64From(input["record_id"])
	if recordID > 0 {
		sets := []string{}
		args := []any{}
		for col, value := range values {
			sets = append(sets, db.Q(col)+"=?")
			args = append(args, value)
		}
		sets = append(sets, "updated_at=NOW()")
		args = append(args, recordID)
		_, err = s.db.ExecContext(r.Context(), `UPDATE `+db.Q(module.Name)+` SET `+strings.Join(sets, ",")+` WHERE `+db.Q(module.PrimaryField)+`=?`, args...)
		return recordID, err
	}
	cols := []string{}
	args := []any{}
	for col, value := range values {
		cols = append(cols, db.Q(col))
		args = append(args, value)
	}
	cols = append(cols, "ice_slug", "created_at", "updated_at")
	args = append(args, db.RandomSlug(20), time.Now(), time.Now())
	res, err := s.db.ExecContext(r.Context(), `INSERT INTO `+db.Q(module.Name)+` (`+strings.Join(cols, ",")+`) VALUES (`+db.Placeholders(len(args))+`)`, args...)
	if err != nil {
		return 0, err
	}
	id, _ := res.LastInsertId()
	_, _ = s.db.ExecContext(r.Context(), `INSERT INTO ice_work_flow_data (from_id,from_module_id,to_id,to_module_id,created_at,updated_at) VALUES (?,?,?,?,NOW(),NOW())`, int64From(input["from_id"]), int64From(input["from_module"]), id, moduleID)
	return id, nil
}

func (s *Server) deleteRecord(w http.ResponseWriter, r *http.Request, path string) {
	parts := strings.Split(path, "/")
	if len(parts) < 4 {
		http.NotFound(w, r)
		return
	}
	id := int64From(parts[1])
	typ := parts[3]
	input := formMap(r)
	if typ != "module" {
		if typ == "relationship" {
			s.deleteRelationshipRecords(w, r, id, input)
			return
		}
		writeJSON(w, http.StatusOK, map[string]any{"success": false})
		return
	}
	module, err := db.GetModule(r.Context(), s.db, id)
	if err != nil || !s.can(r, module.ID, "write") {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "No Access"})
		return
	}
	ids := idsFrom(input)
	if len(ids) == 0 {
		writeJSON(w, http.StatusOK, map[string]any{"success": false})
		return
	}
	args := make([]any, len(ids))
	for i, item := range ids {
		args[i] = item
	}
	res, err := s.db.ExecContext(r.Context(), `DELETE FROM `+db.Q(module.Name)+` WHERE `+db.Q(module.PrimaryField)+` IN (`+db.Placeholders(len(args))+`)`, args...)
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	n, _ := res.RowsAffected()
	writeJSON(w, http.StatusOK, map[string]any{"success": n})
}

func (s *Server) deleteRelationshipRecords(w http.ResponseWriter, r *http.Request, relationshipID int64, input map[string]any) {
	relationship, err := s.relationship(r, relationshipID)
	if err != nil {
		writeJSON(w, http.StatusNotFound, map[string]any{"error": "Relationship not found"})
		return
	}
	moduleID := s.firstRelationshipModuleID(r, relationshipID)
	if moduleID > 0 && !s.can(r, moduleID, "write") {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "No Access"})
		return
	}
	ids := idsFrom(input)
	if len(ids) == 0 {
		writeJSON(w, http.StatusOK, map[string]any{"success": false})
		return
	}
	args := make([]any, len(ids))
	for i, item := range ids {
		args[i] = item
	}
	res, err := s.db.ExecContext(r.Context(), `DELETE FROM `+db.Q(relationship.Name)+` WHERE id IN (`+db.Placeholders(len(args))+`)`, args...)
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	n, _ := res.RowsAffected()
	writeJSON(w, http.StatusOK, map[string]any{"success": n})
}

func (s *Server) saveSubpanel(w http.ResponseWriter, r *http.Request) {
	input := formMap(r)
	subpanelID := int64From(firstAny(input["subpanel_id"], input["subpanelId"]))
	recordID := int64From(input["record_id"])
	if subpanelID == 0 {
		writeJSON(w, http.StatusInternalServerError, map[string]any{"error": "Missing Subpanel ID"})
		return
	}
	subpanel, err := s.subpanel(r, subpanelID)
	if err != nil {
		writeJSON(w, http.StatusNotFound, map[string]any{"error": "Subpanel not found"})
		return
	}
	moduleID := int64From(subpanel["module_id"])
	if moduleID > 0 && !s.can(r, moduleID, "write") {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "No Access"})
		return
	}
	selected := selectedModuleRecords(input)
	for moduleID, fields := range groupedNewRecords(input) {
		fields["module_id"] = moduleID
		fields["from_id"] = input["from_id"]
		fields["from_module"] = input["from_module"]
		id, err := s.saveModuleRecord(r, moduleID, fields)
		if err == nil && id > 0 {
			selected[moduleID] = id
		}
	}
	id, err := s.saveRelationshipRecord(r, int64From(subpanel["relationship_id"]), selected, recordID)
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	writeJSON(w, http.StatusOK, id)
}

func (s *Server) saveRelationshipRecord(r *http.Request, relationshipID int64, records map[int64]int64, recordID int64) (any, error) {
	relationship, err := s.relationship(r, relationshipID)
	if err != nil {
		return nil, err
	}
	if err := db.GenerateRelationshipTables(r.Context(), s.db); err != nil {
		return nil, err
	}
	cols := []string{}
	args := []any{}
	for moduleID, recordValue := range records {
		module, err := db.GetModule(r.Context(), s.db, moduleID)
		if err != nil || !db.ValidIdent(module.Name) {
			continue
		}
		cols = append(cols, db.Q(module.Name+"_id"))
		args = append(args, recordValue)
	}
	if len(cols) == 0 {
		return nil, fmt.Errorf("no relationship records submitted")
	}
	if recordID > 0 {
		sets := make([]string, len(cols))
		for i, col := range cols {
			sets[i] = col + "=?"
		}
		args = append(args, recordID)
		_, err := s.db.ExecContext(r.Context(), `UPDATE `+db.Q(relationship.Name)+` SET `+strings.Join(sets, ",")+`,updated_at=NOW() WHERE id=?`, args...)
		return recordID, err
	}
	cols = append(cols, "status", "created_at", "updated_at")
	args = append(args, 1, time.Now(), time.Now())
	res, err := s.db.ExecContext(r.Context(), `INSERT INTO `+db.Q(relationship.Name)+` (`+strings.Join(cols, ",")+`) VALUES (`+db.Placeholders(len(args))+`)`, args...)
	if err != nil {
		return nil, err
	}
	id, _ := res.LastInsertId()
	return id, nil
}

func (s *Server) search(r *http.Request, input map[string]any, replaceIds bool) map[string]any {
	if fmt.Sprint(firstAny(input["search_type"], r.URL.Query().Get("search_type"))) == "relationship" {
		return s.searchRelationship(r, input)
	}
	moduleID := int64From(firstAny(input["module_id"], r.URL.Query().Get("module_id")))
	if moduleID == 0 {
		return paginate([]map[string]any{}, 1, 10, 0)
	}
	module, err := db.GetModule(r.Context(), s.db, moduleID)
	if err != nil || !db.ValidIdent(module.Name) {
		return paginate([]map[string]any{}, 1, 10, 0)
	}
	fields, _ := db.Fields(r.Context(), s.db, module.ID, "All")
	selects := []string{}
	for _, field := range fields {
		if db.ValidIdent(field.Name) && field.InputType != "password" {
			selects = append(selects, db.Q(module.Name)+"."+db.Q(field.Name)+" AS "+db.Q(module.Name+"__"+field.Name))
		}
	}
	selects = append(selects, db.Q(module.Name)+"."+db.Q(module.PrimaryField)+" AS "+db.Q(module.Name+"_row_id"))
	where := []string{}
	args := []any{}
	for key, value := range input {
		parts := strings.SplitN(key, "__", 2)
		if len(parts) != 2 || int64From(parts[0]) != module.ID || fmt.Sprint(value) == "" || fmt.Sprint(value) == "undefined" {
			continue
		}
		if !db.ValidIdent(parts[1]) {
			continue
		}
		op := "="
		if isStringField(fields, parts[1]) {
			op = "LIKE"
			value = "%" + fmt.Sprint(value) + "%"
		}
		where = append(where, db.Q(module.Name)+"."+db.Q(parts[1])+" "+op+" ?")
		args = append(args, value)
	}
	perPage := intFrom(firstAny(input["per_page"], db.Setting(r.Context(), s.db, "search_per_page")), 10)
	page := intFrom(firstAny(input["page"], r.URL.Query().Get("page")), 1)
	order := fmt.Sprint(firstAny(input["order_by"], module.PrimaryField))
	if strings.Contains(order, "__") {
		order = strings.SplitN(order, "__", 2)[1]
	}
	if !db.ValidIdent(order) {
		order = module.PrimaryField
	}
	dir := strings.ToLower(fmt.Sprint(firstAny(input["search_order"], "asc")))
	if dir != "desc" {
		dir = "asc"
	}
	query := `SELECT ` + strings.Join(selects, ",") + ` FROM ` + db.Q(module.Name)
	countQuery := `SELECT COUNT(*) FROM ` + db.Q(module.Name)
	if len(where) > 0 {
		query += " WHERE " + strings.Join(where, " AND ")
		countQuery += " WHERE " + strings.Join(where, " AND ")
	}
	var total int
	_ = s.db.QueryRowContext(r.Context(), countQuery, args...).Scan(&total)
	query += ` ORDER BY ` + db.Q(order) + ` ` + dir + ` LIMIT ? OFFSET ?`
	rows, err := s.db.QueryContext(r.Context(), query, append(args, perPage, (page-1)*perPage)...)
	if err != nil {
		return paginate([]map[string]any{}, page, perPage, 0)
	}
	items, _ := db.RowsToMaps(rows)
	return paginate(items, page, perPage, total)
}

func (s *Server) searchRelationship(r *http.Request, input map[string]any) map[string]any {
	relationshipID := int64From(firstAny(input["relationship_id"], r.URL.Query().Get("relationship_id")))
	var rel *relationshipMeta
	var err error
	if relationshipID > 0 {
		rel, err = s.relationship(r, relationshipID)
	} else if name := fmt.Sprint(firstAny(input["relationship_name"], r.URL.Query().Get("relationship_name"))); name != "" {
		rel, err = s.relationshipByName(r, name)
	}
	if err != nil || rel == nil {
		return paginate([]map[string]any{}, 1, 10, 0)
	}
	moduleIDs := []int64{}
	if rows, err := s.db.QueryContext(r.Context(), `SELECT module_id FROM ice_relationship_modules WHERE relationship_id=? ORDER BY id`, rel.ID); err == nil {
		for rows.Next() {
			var id int64
			_ = rows.Scan(&id)
			moduleIDs = append(moduleIDs, id)
		}
		_ = rows.Close()
	}
	if len(moduleIDs) == 0 {
		for _, part := range strings.Split(rel.Modules, ",") {
			if id := int64From(strings.TrimSpace(part)); id > 0 {
				moduleIDs = append(moduleIDs, id)
			}
		}
	}
	selects := []string{db.Q(rel.Name) + ".id AS relationship_id"}
	joins := []string{}
	where := []string{}
	args := []any{}
	orderBy := "relationship_id"
	for _, moduleID := range moduleIDs {
		module, err := db.GetModule(r.Context(), s.db, moduleID)
		if err != nil || !db.ValidIdent(module.Name) {
			continue
		}
		joins = append(joins, ` JOIN `+db.Q(module.Name)+` ON `+db.Q(rel.Name)+`.`+db.Q(module.Name+"_id")+`=`+db.Q(module.Name)+`.id`)
		selects = append(selects, db.Q(module.Name)+`.id AS `+db.Q(module.Name+"_row_id"))
		fields, _ := db.Fields(r.Context(), s.db, moduleID, "All")
		for _, field := range fields {
			if db.ValidIdent(field.Name) && field.InputType != "password" {
				selects = append(selects, db.Q(module.Name)+`.`+db.Q(field.Name)+` AS `+db.Q(module.Name+"__"+field.Name))
			}
		}
		for key, value := range input {
			parts := strings.SplitN(key, "__", 2)
			if len(parts) != 2 || int64From(parts[0]) != moduleID || fmt.Sprint(value) == "" || fmt.Sprint(value) == "undefined" {
				continue
			}
			if !db.ValidIdent(parts[1]) {
				continue
			}
			op := "="
			if isStringField(fields, parts[1]) {
				op = "LIKE"
				value = "%" + fmt.Sprint(value) + "%"
			}
			where = append(where, db.Q(module.Name)+"."+db.Q(parts[1])+" "+op+" ?")
			args = append(args, value)
		}
		orderBy = module.Name + "_row_id"
	}
	perPage := intFrom(firstAny(input["per_page"], db.Setting(r.Context(), s.db, "search_per_page")), 10)
	page := intFrom(firstAny(input["page"], r.URL.Query().Get("page")), 1)
	query := `SELECT ` + strings.Join(selects, ",") + ` FROM ` + db.Q(rel.Name) + strings.Join(joins, "")
	countQuery := `SELECT COUNT(*) FROM ` + db.Q(rel.Name) + strings.Join(joins, "")
	if len(where) > 0 {
		query += " WHERE " + strings.Join(where, " AND ")
		countQuery += " WHERE " + strings.Join(where, " AND ")
	}
	var total int
	_ = s.db.QueryRowContext(r.Context(), countQuery, args...).Scan(&total)
	order := fmt.Sprint(firstAny(input["order_by"], orderBy))
	if strings.Contains(order, "__") {
		pieces := strings.SplitN(order, "__", 2)
		if moduleID := int64From(pieces[0]); moduleID > 0 {
			if module, err := db.GetModule(r.Context(), s.db, moduleID); err == nil && db.ValidIdent(module.Name) && db.ValidIdent(pieces[1]) {
				order = module.Name + "." + pieces[1]
			}
		}
	}
	orderExpr := db.Q(order)
	if strings.Contains(order, ".") {
		pieces := strings.SplitN(order, ".", 2)
		if db.ValidIdent(pieces[0]) && db.ValidIdent(pieces[1]) {
			orderExpr = db.Q(pieces[0]) + "." + db.Q(pieces[1])
		}
	}
	dir := strings.ToLower(fmt.Sprint(firstAny(input["search_order"], "asc")))
	if dir != "desc" {
		dir = "asc"
	}
	query += ` ORDER BY ` + orderExpr + ` ` + dir + ` LIMIT ? OFFSET ?`
	rows, err := s.db.QueryContext(r.Context(), query, append(args, perPage, (page-1)*perPage)...)
	if err != nil {
		return paginate([]map[string]any{}, page, perPage, 0)
	}
	items, _ := db.RowsToMaps(rows)
	return paginate(items, page, perPage, total)
}

func paginate(items []map[string]any, page, perPage, total int) map[string]any {
	last := int(math.Ceil(float64(total) / float64(perPage)))
	if last < 1 {
		last = 1
	}
	return map[string]any{"data": items, "current_page": page, "per_page": perPage, "total": total, "last_page": last, "from": (page-1)*perPage + 1, "to": (page-1)*perPage + len(items)}
}

func (s *Server) dataModuleRecord(w http.ResponseWriter, r *http.Request, path string) {
	parts := strings.Split(path, "/")
	if len(parts) < 4 {
		http.NotFound(w, r)
		return
	}
	moduleID := int64From(parts[1])
	recordID := int64From(parts[3])
	module, err := db.GetModule(r.Context(), s.db, moduleID)
	if err != nil || !s.can(r, moduleID, "read") {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "No Access"})
		return
	}
	writeJSON(w, http.StatusOK, s.getRecord(r, module, recordID))
}

func (s *Server) dataSearchFields(w http.ResponseWriter, r *http.Request, path string) {
	parts := strings.Split(path, "/")
	if len(parts) < 3 {
		http.NotFound(w, r)
		return
	}
	moduleID := int64From(parts[1])
	mode := "All"
	if len(parts) >= 4 {
		mode = parts[3]
	}
	if mode == "relationship" {
		fields := map[string]db.Field{}
		if rel, err := s.relationship(r, moduleID); err == nil {
			for _, rawModuleID := range strings.Split(rel.Modules, ",") {
				moduleFields, _ := db.Fields(r.Context(), s.db, int64From(strings.TrimSpace(rawModuleID)), "All")
				for key, field := range db.FieldMap(moduleFields) {
					fields[key] = field
				}
			}
		}
		writeJSON(w, http.StatusOK, fields)
		return
	}
	fields, _ := db.Fields(r.Context(), s.db, moduleID, mode)
	writeJSON(w, http.StatusOK, db.FieldMap(fields))
}

func (s *Server) builder(w http.ResponseWriter, r *http.Request, path string) {
	parts := strings.Split(path, "/")
	if len(parts) < 4 {
		http.NotFound(w, r)
		return
	}
	id := int64From(parts[1])
	typ := parts[3]
	input := formMap(r)
	switch typ {
	case "get_modules":
		items, _ := db.Modules(r.Context(), s.db, false)
		writeJSON(w, http.StatusOK, map[string]any{"modules": items})
	case "get_datalets":
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_datalets`)
		writeJSON(w, http.StatusOK, map[string]any{"datalets": items})
	case "get_relationships":
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_relationships`)
		writeJSON(w, http.StatusOK, map[string]any{"relationships": items})
	case "select_module":
		module, _ := db.GetModule(r.Context(), s.db, id)
		fields, _ := db.Fields(r.Context(), s.db, id, "All")
		subpanels, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_module_subpanels WHERE module_id=?`, id)
		relationships, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_relationships WHERE modules LIKE ?`, "%"+strconv.FormatInt(id, 10)+"%")
		writeJSON(w, http.StatusOK, map[string]any{"module": module, "fields": fields, "subpanels": subpanels, "relationships": relationships})
	case "regenerate":
		err := db.GenerateModuleTables(r.Context(), s.db)
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "add_module":
		name := fmt.Sprint(input["name"])
		res, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_modules (name,label,module_group_id,status,create_table,icon,created_at,updated_at) VALUES (?,?,6,1,1,'CircleStackIcon',NOW(),NOW())`, name, strings.Title(strings.ReplaceAll(name, "_", " ")))
		if err == nil {
			moduleID, _ := res.LastInsertId()
			_, _ = s.db.ExecContext(r.Context(), `INSERT INTO ice_permissions (role_id,module_id,created_at,updated_at) VALUES (1,?,NOW(),NOW())`, moduleID)
		}
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "add_field":
		name := fmt.Sprint(input["name"])
		_, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_fields (name,label,data_type,field_length,status,module_id,created_at,updated_at) VALUES (?,?,'string',64,0,?,NOW(),NOW())`, name, strings.Title(strings.ReplaceAll(name, "_", " ")), id)
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "add_subpanel":
		name := fmt.Sprint(input["name"])
		_, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_module_subpanels (name,label,module_id,status,created_at,updated_at) VALUES (?,?,?,0,NOW(),NOW())`, name, strings.Title(strings.ReplaceAll(name, "_", " ")), id)
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "add_relationship":
		name := fmt.Sprint(input["name"])
		_, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_relationships (name,label,modules,status,created_at,updated_at) VALUES (?,?,?,0,NOW(),NOW())`, name, strings.Title(strings.ReplaceAll(name, "_", " ")), fmt.Sprint(input["relationship_modules"]))
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "add_datalet":
		name := fmt.Sprint(input["name"])
		_, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_datalets (name,label,active,created_at,updated_at) VALUES (?,?,0,NOW(),NOW())`, name, strings.Title(strings.ReplaceAll(name, "_", " ")))
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "add_subpanel_field":
		_, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_subpanel_fields (field_id,subpanel_id,created_at,updated_at) VALUES (?,?,NOW(),NOW())`, input["subpanel_field_id"], input["subpanel_id"])
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "delete_subpanel_field":
		_, err := s.db.ExecContext(r.Context(), `DELETE FROM ice_subpanel_fields WHERE field_id=? AND subpanel_id=?`, input["subpanel_field_id"], input["subpanel_id"])
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "select_subpanel_fields":
		subpanel, err := s.subpanel(r, id)
		if err != nil {
			writeJSON(w, http.StatusNotFound, map[string]any{"error": "Subpanel not found"})
			return
		}
		fields := []db.Field{}
		if rel, err := s.relationship(r, int64From(subpanel["relationship_id"])); err == nil {
			for _, rawModuleID := range strings.Split(rel.Modules, ",") {
				moduleFields, _ := db.Fields(r.Context(), s.db, int64From(strings.TrimSpace(rawModuleID)), "All")
				fields = append(fields, moduleFields...)
			}
		}
		writeJSON(w, http.StatusOK, fields)
	case "save":
		table := map[string]string{"module": "ice_modules", "field": "ice_fields", "subpanel": "ice_module_subpanels", "relationship": "ice_relationships", "datalet": "ice_datalets"}[fmt.Sprint(input["type"])]
		key := fmt.Sprint(input["key"])
		if table == "" || !db.ValidIdent(key) {
			writeJSON(w, http.StatusBadRequest, map[string]any{"status": 0})
			return
		}
		_, err := s.db.ExecContext(r.Context(), `UPDATE `+table+` SET `+db.Q(key)+`=?,updated_at=NOW() WHERE id=?`, input["value"], input["type_id"])
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case "delete":
		table := map[string]string{"module": "ice_modules", "field": "ice_fields", "subpanel": "ice_module_subpanels", "relationship": "ice_relationships", "datalet": "ice_datalets"}[fmt.Sprint(input["type"])]
		if table == "" {
			writeJSON(w, http.StatusBadRequest, map[string]any{"status": 0})
			return
		}
		_, err := s.db.ExecContext(r.Context(), `DELETE FROM `+table+` WHERE id=?`, input["delete_id"])
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	default:
		writeJSON(w, http.StatusOK, map[string]any{"status": 0})
	}
}

func (s *Server) connectorData(w http.ResponseWriter, r *http.Request, path string) {
	switch {
	case path == "connectors":
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_connectors`)
		writeJSON(w, http.StatusOK, items)
	case path == "connector/set_connector":
		input := formMap(r)
		res, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_connectors (id,name,auth_type,auth_key,base_url,token_url,client_id,client_secret,username,password,access_token,refresh_token,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW()) ON DUPLICATE KEY UPDATE name=VALUES(name),auth_type=VALUES(auth_type),auth_key=VALUES(auth_key),base_url=VALUES(base_url),token_url=VALUES(token_url),client_id=VALUES(client_id),client_secret=VALUES(client_secret),username=VALUES(username),password=VALUES(password),access_token=VALUES(access_token),refresh_token=VALUES(refresh_token),status=VALUES(status),updated_at=NOW()`,
			nullZero(input["id"]), input["name"], input["auth_type"], input["auth_key"], input["base_url"], input["token_url"], input["client_id"], input["client_secret"], input["username"], input["password"], input["access_token"], input["refresh_token"], input["status"])
		id := int64From(input["id"])
		if id == 0 && err == nil && res != nil {
			id, _ = res.LastInsertId()
		}
		connector, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_connectors WHERE id=? LIMIT 1`, id)
		var payload any = map[string]any{"id": id}
		if len(connector) > 0 {
			payload = connector[0]
		}
		writeJSON(w, status(err), map[string]any{"id": id, "status": "Connector saved successfully", "connector": payload})
	case strings.HasPrefix(path, "connector/delete_connector/"):
		id := strings.TrimPrefix(path, "connector/delete_connector/")
		_, _ = s.db.ExecContext(r.Context(), `DELETE FROM ice_connector_commands WHERE connector_id=?`, id)
		_, err := s.db.ExecContext(r.Context(), `DELETE FROM ice_connectors WHERE id=?`, id)
		writeJSON(w, status(err), map[string]any{"status": "Connector and its commands deleted successfully"})
	case strings.HasPrefix(path, "connectors/"):
		id := strings.TrimPrefix(path, "connectors/")
		_, _ = s.db.ExecContext(r.Context(), `DELETE FROM ice_connector_commands WHERE connector_id=?`, id)
		_, _ = s.db.ExecContext(r.Context(), `DELETE FROM ice_endpoints WHERE connector_id=?`, id)
		_, err := s.db.ExecContext(r.Context(), `DELETE FROM ice_connectors WHERE id=?`, id)
		writeJSON(w, status(err), map[string]any{"status": "Connector deleted"})
	case strings.HasPrefix(path, "connector/delete_command/"):
		_, err := s.db.ExecContext(r.Context(), `DELETE FROM ice_connector_commands WHERE id=?`, strings.TrimPrefix(path, "connector/delete_command/"))
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case path == "connector/add_command":
		input := formMap(r)
		res, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_connector_commands (connector_id,name,method_name,description,endpoint_id,status,class_name,created_at,updated_at) VALUES (?,?,?,?,?,?,?,NOW(),NOW())`, input["connector_id"], input["name"], input["method_name"], input["description"], input["endpoint_id"], input["status"], input["class_name"])
		var id int64
		if err == nil && res != nil {
			id, _ = res.LastInsertId()
		}
		command := map[string]any{"id": id}
		if items, qErr := queryMaps(r.Context(), s.db, `SELECT * FROM ice_connector_commands WHERE id=? LIMIT 1`, id); qErr == nil && len(items) > 0 {
			command = items[0]
		}
		writeJSON(w, status(err), map[string]any{"success": err == nil, "command": command})
	case strings.HasPrefix(path, "connector/update_command/"):
		id := strings.TrimPrefix(path, "connector/update_command/")
		input := formMap(r)
		_, err := s.db.ExecContext(r.Context(), `UPDATE ice_connector_commands SET name=?,method_name=?,description=?,endpoint_id=?,status=?,class_name=?,updated_at=NOW() WHERE id=?`, input["name"], input["method_name"], input["description"], input["endpoint_id"], input["status"], input["class_name"], id)
		writeJSON(w, status(err), map[string]any{"success": err == nil})
	case path == "connector/run_command":
		input := formMap(r)
		s.runConnectorCommand(w, r, fmt.Sprint(firstAny(input["command_id"], input["id"])))
	case strings.HasPrefix(path, "commands/"):
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_connector_commands WHERE connector_id=? ORDER BY id`, strings.TrimPrefix(path, "commands/"))
		writeJSON(w, http.StatusOK, items)
	case path == "endpoints/add":
		input := formMap(r)
		res, err := s.db.ExecContext(r.Context(), `INSERT INTO ice_endpoints (connector_id,name,endpoint,request_type,class_name,params,headers,status,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,NOW(),NOW())`, input["connector_id"], input["name"], input["endpoint"], firstAny(input["request_type"], "GET"), input["class_name"], input["params"], input["headers"], firstAny(input["status"], 1))
		id, _ := res.LastInsertId()
		writeJSON(w, status(err), map[string]any{"id": id})
	case strings.HasPrefix(path, "endpoints/update/"):
		id := strings.TrimPrefix(path, "endpoints/update/")
		input := formMap(r)
		_, err := s.db.ExecContext(r.Context(), `UPDATE ice_endpoints SET name=?,endpoint=?,request_type=?,class_name=?,params=?,headers=?,status=?,updated_at=NOW() WHERE id=?`, input["name"], input["endpoint"], input["request_type"], input["class_name"], input["params"], input["headers"], input["status"], id)
		writeJSON(w, status(err), map[string]any{"message": "Endpoint updated successfully"})
	case strings.HasPrefix(path, "endpoints/delete/"):
		_, err := s.db.ExecContext(r.Context(), `DELETE FROM ice_endpoints WHERE id=?`, strings.TrimPrefix(path, "endpoints/delete/"))
		writeJSON(w, status(err), map[string]any{"status": err == nil})
	case strings.HasPrefix(path, "endpoints/"):
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_endpoints WHERE connector_id=?`, strings.TrimPrefix(path, "endpoints/"))
		writeJSON(w, http.StatusOK, items)
	case strings.HasPrefix(path, "connector/command/"):
		item, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_connector_commands WHERE id=? LIMIT 1`, strings.TrimPrefix(path, "connector/command/"))
		if len(item) == 0 {
			writeJSON(w, http.StatusNotFound, map[string]any{"error": "Not found"})
			return
		}
		writeJSON(w, http.StatusOK, map[string]any{"command": item[0]})
	case path == "workflow/save":
		s.saveWorkflow(w, r)
	case strings.HasPrefix(path, "workflow/"):
		_, err := s.db.ExecContext(r.Context(), `DELETE FROM ice_module_convertables WHERE id=?`, strings.TrimPrefix(path, "workflow/"))
		writeJSON(w, status(err), map[string]any{"success": err == nil})
	default:
		http.NotFound(w, r)
	}
}

func (s *Server) adminModuleData(w http.ResponseWriter, r *http.Request) {
	typ := firstAny(r.URL.Query().Get("type"), r.FormValue("type"))
	id := int64From(firstAny(r.URL.Query().Get("id"), r.FormValue("id")))
	switch fmt.Sprint(typ) {
	case "module":
		module, err := db.GetModule(r.Context(), s.db, id)
		if err != nil {
			writeJSON(w, http.StatusNotFound, map[string]any{"error": "Module not found"})
			return
		}
		fields, _ := db.Fields(r.Context(), s.db, id, "All")
		subpanels, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_module_subpanels WHERE module_id=?`, id)
		groups, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_module_groups WHERE id=? LIMIT 1`, module.ModuleGroupID)
		converted, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_module_convertables WHERE primary_module_id=? LIMIT 1`, id)
		writeJSON(w, http.StatusOK, map[string]any{"module": module, "fields": fields, "groups": firstMap(groups), "convertedmodules": firstMap(converted), "subpanels": subpanels})
	case "subpanel":
		subpanel, err := s.subpanel(r, id)
		if err != nil {
			writeJSON(w, http.StatusNotFound, map[string]any{"error": "Subpanel not found"})
			return
		}
		rel, _ := s.relationship(r, int64From(subpanel["relationship_id"]))
		module, _ := db.GetModule(r.Context(), s.db, int64From(subpanel["module_id"]))
		subpanel["relationship"] = rel
		subpanel["module"] = module
		writeJSON(w, http.StatusOK, subpanel)
	default:
		writeJSON(w, http.StatusOK, map[string]any{})
	}
}

func (s *Server) sendEndpointRequest(w http.ResponseWriter, r *http.Request) {
	id := int64From(firstAny(r.URL.Query().Get("id"), r.FormValue("id")))
	items, err := queryMaps(r.Context(), s.db, `SELECT e.*,c.base_url,c.auth_key FROM ice_endpoints e LEFT JOIN ice_connectors c ON c.id=e.connector_id WHERE e.id=? LIMIT 1`, id)
	if err != nil || len(items) == 0 {
		writeJSON(w, http.StatusNotFound, map[string]any{"error": "Endpoint not found"})
		return
	}
	endpoint := items[0]
	baseURL := strings.TrimRight(fmt.Sprint(endpoint["base_url"]), "/")
	path := strings.TrimLeft(fmt.Sprint(endpoint["endpoint"]), "/")
	if baseURL == "" || baseURL == "<nil>" || path == "" || path == "<nil>" {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "Endpoint is not configured"})
		return
	}
	method := strings.ToUpper(fmt.Sprint(firstAny(endpoint["request_type"], "GET")))
	req, err := http.NewRequestWithContext(r.Context(), method, baseURL+"/"+path, nil)
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	if authKey := fmt.Sprint(endpoint["auth_key"]); authKey != "" && authKey != "<nil>" {
		req.Header.Set("Authorization", "Bearer "+authKey)
	}
	resp, err := http.DefaultClient.Do(req)
	if err != nil {
		writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": err.Error()})
		return
	}
	defer resp.Body.Close()
	body, _ := io.ReadAll(resp.Body)
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(resp.StatusCode)
	_, _ = w.Write(body)
}

func (s *Server) saveWorkflow(w http.ResponseWriter, r *http.Request) {
	input := formMap(r)
	raw, ok := input["workflow"].([]any)
	if !ok {
		writeJSON(w, http.StatusOK, map[string]any{"success": true})
		return
	}
	incoming := map[int64]bool{}
	for index, item := range raw {
		row, ok := item.(map[string]any)
		if !ok {
			continue
		}
		moduleID := int64From(row["module_id"])
		if moduleID == 0 {
			continue
		}
		incoming[moduleID] = true
		primaryID := int64From(firstAny(row["primary_module_id"], moduleID))
		var existingID int64
		_ = s.db.QueryRowContext(r.Context(), `SELECT id FROM ice_module_convertables WHERE module_id=? LIMIT 1`, moduleID).Scan(&existingID)
		if existingID > 0 {
			_, _ = s.db.ExecContext(r.Context(), `UPDATE ice_module_convertables SET primary_module_id=?,level=?,updated_at=NOW() WHERE id=?`, primaryID, index+1, existingID)
		} else {
			_, _ = s.db.ExecContext(r.Context(), `INSERT INTO ice_module_convertables (primary_module_id,module_id,level,created_at,updated_at) VALUES (?,?,?,NOW(),NOW())`, primaryID, moduleID, index+1)
		}
	}
	existing, _ := queryMaps(r.Context(), s.db, `SELECT module_id FROM ice_module_convertables`)
	for _, row := range existing {
		moduleID := int64From(row["module_id"])
		if moduleID > 0 && !incoming[moduleID] {
			_, _ = s.db.ExecContext(r.Context(), `DELETE FROM ice_module_convertables WHERE module_id=?`, moduleID)
		}
	}
	writeJSON(w, http.StatusOK, map[string]any{"success": true})
}

func (s *Server) runConnectorCommand(w http.ResponseWriter, r *http.Request, rawID string) {
	id := int64From(rawID)
	items, err := queryMaps(r.Context(), s.db, `SELECT cc.*,c.base_url,c.auth_type,c.auth_key,c.username,c.password,e.endpoint,e.request_type,e.params,e.headers FROM ice_connector_commands cc LEFT JOIN ice_connectors c ON c.id=cc.connector_id LEFT JOIN ice_endpoints e ON e.id=cc.endpoint_id WHERE cc.id=? LIMIT 1`, id)
	if err != nil || len(items) == 0 {
		writeJSON(w, http.StatusNotFound, map[string]any{"status": "fail", "message": "Command not found"})
		return
	}
	command := items[0]
	baseURL := strings.TrimRight(fmt.Sprint(command["base_url"]), "/")
	endpoint := fmt.Sprint(command["endpoint"])
	if endpoint == "" || endpoint == "<nil>" {
		endpoint = fmt.Sprint(command["method_name"])
	}
	if endpoint == "" || endpoint == "<nil>" || baseURL == "" || baseURL == "<nil>" {
		message := "Command has no connector endpoint configured"
		_, _ = s.db.ExecContext(r.Context(), `UPDATE ice_connector_commands SET last_run_status='fail',last_run_message=?,last_run_data=?,last_updated=NOW() WHERE id=?`, message, "{}", id)
		writeJSON(w, http.StatusOK, map[string]any{"status": "fail", "message": message})
		return
	}
	target := baseURL + "/" + strings.TrimLeft(endpoint, "/")
	method := strings.ToUpper(fmt.Sprint(firstAny(command["request_type"], "GET")))
	req, err := http.NewRequestWithContext(r.Context(), method, target, nil)
	if err != nil {
		writeJSON(w, http.StatusOK, s.recordCommandFailure(r, id, err))
		return
	}
	if authKey := fmt.Sprint(command["auth_key"]); authKey != "" && authKey != "<nil>" {
		req.Header.Set("Authorization", "Bearer "+authKey)
	}
	if headers := fmt.Sprint(command["headers"]); headers != "" && headers != "<nil>" {
		for _, line := range strings.Split(headers, "\n") {
			parts := strings.SplitN(line, ":", 2)
			if len(parts) == 2 {
				req.Header.Set(strings.TrimSpace(parts[0]), strings.TrimSpace(parts[1]))
			}
		}
	}
	resp, err := http.DefaultClient.Do(req)
	if err != nil {
		writeJSON(w, http.StatusOK, s.recordCommandFailure(r, id, err))
		return
	}
	defer resp.Body.Close()
	body, _ := io.ReadAll(resp.Body)
	statusText := "success"
	if resp.StatusCode >= 400 {
		statusText = "fail"
	}
	_, _ = s.db.ExecContext(r.Context(), `UPDATE ice_connector_commands SET last_run_status=?,last_run_message=?,last_run_data=?,last_updated=NOW() WHERE id=?`, statusText, resp.Status, string(body), id)
	w.Header().Set("Content-Type", "application/json")
	w.WriteHeader(http.StatusOK)
	_, _ = w.Write(body)
}

func (s *Server) recordCommandFailure(r *http.Request, id int64, err error) map[string]any {
	_, _ = s.db.ExecContext(r.Context(), `UPDATE ice_connector_commands SET last_run_status='fail',last_run_message=?,last_run_data=?,last_updated=NOW() WHERE id=?`, err.Error(), "{}", id)
	return map[string]any{"status": "fail", "message": err.Error()}
}

func (s *Server) aiAssistFields(w http.ResponseWriter, r *http.Request, rawModuleID string) {
	moduleID := int64From(rawModuleID)
	fields, _ := db.Fields(r.Context(), s.db, moduleID, "All")
	existing := map[string]bool{}
	for _, field := range fields {
		existing[strings.ToLower(field.Name)] = true
	}
	suggestions := []map[string]any{}
	for _, name := range []string{"name", "description", "status", "assigned_to"} {
		if existing[name] {
			continue
		}
		suggestions = append(suggestions, map[string]any{
			"name":         name,
			"label":        strings.Title(strings.ReplaceAll(name, "_", " ")),
			"input_type":   "text",
			"data_type":    "string",
			"field_length": 245,
		})
	}
	writeJSON(w, http.StatusOK, suggestions)
}

func (s *Server) activeDatalets(r *http.Request) []map[string]any {
	user := s.currentUser(r)
	items, _ := queryMaps(r.Context(), s.db, `SELECT d.*,t.name AS type_name FROM ice_datalets d LEFT JOIN ice_datalet_types t ON t.id=d.type WHERE d.role_id=0 OR d.role_id=? ORDER BY d.display_order`, user.RoleID)
	out := []map[string]any{}
	for _, item := range items {
		out = append(out, map[string]any{"datalet": item, "data": s.dataletData(r, int64From(item["id"]))})
	}
	return out
}

func (s *Server) dataletData(r *http.Request, id int64) any {
	var typ int
	_ = s.db.QueryRowContext(r.Context(), `SELECT type FROM ice_datalets WHERE id=?`, id).Scan(&typ)
	switch typ {
	case 1:
		return map[string]any{"labels": []string{"Tax", "Discount", "Gross", "Net"}, "data": []float64{round2(s.sumColumn(r, "lineitems", "taxes") / 10), round2(s.sumColumn(r, "lineitems", "discount") / 5), s.sumColumn(r, "lineitems", "gross"), round2(s.sumColumn(r, "lineitems", "gross") / 2)}}
	case 2:
		return map[string]any{"labels": []string{"Leads", "Contacts", "Accounts"}, "data": []int64{s.countRecent(r, "leads", 7), s.countRecent(r, "contacts", 7), s.countRecent(r, "accounts", 7)}}
	case 3:
		return map[string]any{"labels": []string{"Today", "Last 7 Days", "Last 30 Days"}, "data": []int64{s.countRecent(r, "meetings", 1), s.countRecent(r, "meetings", 7), s.countRecent(r, "meetings", 30)}}
	case 4:
		return map[string]any{"labels": []string{"Opportunities", "Quotes", "Contracts"}, "data": []int64{countTable(r, s.db, "opportunities"), countTable(r, s.db, "quotes"), countTable(r, s.db, "contracts")}}
	case 5:
		return map[string]any{"labels": []string{"Tax", "Discount", "Gross", "Net"}, "data": []float64{s.sumRecent(r, "invoices", "tax", 30), s.sumRecent(r, "invoices", "discount", 30), s.sumRecent(r, "invoices", "subtotal", 30), s.sumRecent(r, "invoices", "total", 30)}}
	case 6:
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM meetings WHERE status > 0 ORDER BY updated_at DESC LIMIT 1`)
		if len(items) > 0 {
			if typID := int64From(items[0]["types"]); typID > 0 {
				var name string
				_ = s.db.QueryRowContext(r.Context(), `SELECT name FROM meeting_types WHERE id=? LIMIT 1`, typID).Scan(&name)
				items[0]["type"] = name
			}
		}
		return items
	case 7:
		return map[string]any{"modules": countTable(r, s.db, "ice_modules"), "fields": countTable(r, s.db, "ice_fields"), "subpanels": countTable(r, s.db, "ice_module_subpanels"), "relationships": countTable(r, s.db, "ice_relationships")}
	case 8:
		modules, _ := db.Modules(r.Context(), s.db, true)
		out := []map[string]any{}
		for _, module := range modules {
			name := fmt.Sprint(module["name"])
			if db.ValidIdent(name) {
				out = append(out, map[string]any{"name": strings.Title(name), "value": countTable(r, s.db, name), "class": "primary"})
			}
			if len(out) >= 5 {
				break
			}
		}
		return out
	default:
		return map[string]any{"labels": []string{}, "data": []int{}}
	}
}

func (s *Server) resetCRM(r *http.Request) bool {
	modules, err := queryMaps(r.Context(), s.db, `SELECT name FROM ice_modules WHERE faker_seed=1 AND admin<>1`)
	if err != nil {
		return false
	}
	for _, module := range modules {
		name := fmt.Sprint(module["name"])
		if !db.ValidIdent(name) {
			continue
		}
		if _, err := s.db.ExecContext(r.Context(), `TRUNCATE TABLE `+db.Q(name)); err != nil {
			return false
		}
	}
	return true
}

func (s *Server) sumColumn(r *http.Request, table, column string) float64 {
	if !db.ValidIdent(table) || !db.ValidIdent(column) {
		return 0
	}
	var total sql.NullFloat64
	_ = s.db.QueryRowContext(r.Context(), `SELECT SUM(`+db.Q(column)+`) FROM `+db.Q(table)).Scan(&total)
	if total.Valid {
		return total.Float64
	}
	return 0
}

func (s *Server) sumRecent(r *http.Request, table, column string, days int) float64 {
	if !db.ValidIdent(table) || !db.ValidIdent(column) {
		return 0
	}
	var total sql.NullFloat64
	_ = s.db.QueryRowContext(r.Context(), `SELECT SUM(`+db.Q(column)+`) FROM `+db.Q(table)+` WHERE created_at > DATE_SUB(NOW(), INTERVAL ? DAY)`, days).Scan(&total)
	if total.Valid {
		return total.Float64
	}
	return 0
}

func (s *Server) countRecent(r *http.Request, table string, days int) int64 {
	if !db.ValidIdent(table) {
		return 0
	}
	var total int64
	_ = s.db.QueryRowContext(r.Context(), `SELECT COUNT(id) FROM `+db.Q(table)+` WHERE created_at > DATE_SUB(NOW(), INTERVAL ? DAY)`, days).Scan(&total)
	return total
}

func round2(v float64) float64 {
	return math.Round(v*100) / 100
}

func (s *Server) subpanelIDs(r *http.Request, moduleID int64) []int64 {
	rows, err := s.db.QueryContext(r.Context(), `SELECT id FROM ice_module_subpanels WHERE module_id=?`, moduleID)
	if err != nil {
		return nil
	}
	defer rows.Close()
	var ids []int64
	for rows.Next() {
		var id int64
		_ = rows.Scan(&id)
		ids = append(ids, id)
	}
	return ids
}

func (s *Server) subpanelData(r *http.Request, id int64) map[string]any {
	items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_module_subpanels WHERE id=? LIMIT 1`, id)
	if len(items) == 0 {
		return map[string]any{}
	}
	fields, _ := queryMaps(r.Context(), s.db, `SELECT sf.*,f.name,f.label FROM ice_subpanel_fields sf JOIN ice_fields f ON f.id=sf.field_id WHERE sf.subpanel_id=?`, id)
	input := queryAsAnyMap(r)
	input["relationship_id"] = items[0]["relationship_id"]
	input["search_type"] = "relationship"
	if input["per_page"] == nil {
		input["per_page"] = db.Setting(r.Context(), s.db, "submodule_search_per_page")
	}
	if input["search_field"] != nil && input["search_text"] != nil && len(fmt.Sprint(input["search_text"])) > 2 {
		input[fmt.Sprint(input["search_field"])] = input["search_text"]
	}
	return map[string]any{"id": id, "name": items[0]["name"], "label": items[0]["label"], "relationship_id": items[0]["relationship_id"], "fields": fields, "data": s.searchRelationship(r, input)}
}

func (s *Server) relatedFieldData(r *http.Request, moduleID int64) map[string]any {
	fields, _ := db.Fields(r.Context(), s.db, moduleID, "All")
	out := map[string]any{}
	for _, field := range fields {
		if field.RelatedModuleID > 0 {
			related, err := db.GetModule(r.Context(), s.db, field.RelatedModuleID)
			if err == nil && db.ValidIdent(related.Name) {
				out[field.Name], _ = queryMaps(r.Context(), s.db, `SELECT * FROM `+db.Q(related.Name))
			}
		}
	}
	return out
}

func (s *Server) relatedFields(w http.ResponseWriter, r *http.Request, raw string) {
	fieldID := int64From(raw)
	var moduleID int64
	var relatedField, relatedValue string
	err := s.db.QueryRowContext(r.Context(), `SELECT related_module_id,related_field_id,related_value_id FROM ice_fields WHERE id=?`, fieldID).Scan(&moduleID, &relatedField, &relatedValue)
	module, mErr := db.GetModule(r.Context(), s.db, moduleID)
	if err != nil || mErr != nil || !db.ValidIdent(module.Name) || !db.ValidIdent(relatedField) || !db.ValidIdent(relatedValue) {
		writeJSON(w, http.StatusOK, []any{})
		return
	}
	items, _ := queryMaps(r.Context(), s.db, `SELECT `+db.Q(relatedField)+`,`+db.Q(relatedValue)+` FROM `+db.Q(module.Name))
	writeJSON(w, http.StatusOK, items)
}

func (s *Server) relatedFieldName(w http.ResponseWriter, r *http.Request, path string) {
	parts := strings.Split(path, "/")
	if len(parts) < 5 {
		http.NotFound(w, r)
		return
	}
	fieldID := int64From(parts[2])
	value := parts[4]
	var moduleID int64
	var relatedField, relatedValue string
	err := s.db.QueryRowContext(r.Context(), `SELECT related_module_id,related_field_id,related_value_id FROM ice_fields WHERE id=?`, fieldID).Scan(&moduleID, &relatedField, &relatedValue)
	module, mErr := db.GetModule(r.Context(), s.db, moduleID)
	if err != nil || mErr != nil || !db.ValidIdent(module.Name) || !db.ValidIdent(relatedField) || !db.ValidIdent(relatedValue) {
		writeJSON(w, http.StatusOK, "Unknown")
		return
	}
	var out sql.NullString
	_ = s.db.QueryRowContext(r.Context(), `SELECT `+db.Q(relatedValue)+` FROM `+db.Q(module.Name)+` WHERE `+db.Q(relatedField)+`=? LIMIT 1`, value).Scan(&out)
	if !out.Valid || out.String == "" {
		writeJSON(w, http.StatusOK, "Unknown")
		return
	}
	writeJSON(w, http.StatusOK, out.String)
}

func (s *Server) workflowData(r *http.Request, moduleID, recordID int64) []map[string]any {
	items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_work_flow_data WHERE (from_module_id=? AND from_id=?) OR (to_module_id=? AND to_id=?)`, moduleID, recordID, moduleID, recordID)
	return items
}

func (s *Server) nextID(r *http.Request, module *db.Module, recordID int64, op string) int64 {
	if op != "<" && op != ">" {
		return 0
	}
	order := "ASC"
	if op == "<" {
		order = "DESC"
	}
	var id int64
	_ = s.db.QueryRowContext(r.Context(), `SELECT `+db.Q(module.PrimaryField)+` FROM `+db.Q(module.Name)+` WHERE `+db.Q(module.PrimaryField)+op+`? ORDER BY `+db.Q(module.PrimaryField)+` `+order+` LIMIT 1`, recordID).Scan(&id)
	return id
}

type relationshipMeta struct {
	ID      int64
	Name    string
	Modules string
	Status  int
}

func (s *Server) relationship(r *http.Request, id int64) (*relationshipMeta, error) {
	item := &relationshipMeta{}
	err := s.db.QueryRowContext(r.Context(), `SELECT id,name,modules,status FROM ice_relationships WHERE id=? LIMIT 1`, id).Scan(&item.ID, &item.Name, &item.Modules, &item.Status)
	if err != nil {
		return nil, err
	}
	if !db.ValidIdent(item.Name) {
		return nil, fmt.Errorf("invalid relationship table")
	}
	return item, nil
}

func (s *Server) relationshipByName(r *http.Request, name string) (*relationshipMeta, error) {
	item := &relationshipMeta{}
	err := s.db.QueryRowContext(r.Context(), `SELECT id,name,modules,status FROM ice_relationships WHERE name=? LIMIT 1`, name).Scan(&item.ID, &item.Name, &item.Modules, &item.Status)
	if err != nil {
		return nil, err
	}
	if !db.ValidIdent(item.Name) {
		return nil, fmt.Errorf("invalid relationship table")
	}
	return item, nil
}

func (s *Server) subpanel(r *http.Request, id int64) (map[string]any, error) {
	items, err := queryMaps(r.Context(), s.db, `SELECT * FROM ice_module_subpanels WHERE id=? LIMIT 1`, id)
	if err != nil {
		return nil, err
	}
	if len(items) == 0 {
		return nil, sql.ErrNoRows
	}
	return items[0], nil
}

func (s *Server) subpanelPayload(r *http.Request, id int64) (map[string]any, error) {
	subpanel, err := s.subpanel(r, id)
	if err != nil {
		return nil, err
	}
	rel, err := s.relationship(r, int64From(subpanel["relationship_id"]))
	if err == nil {
		relationship := map[string]any{"id": rel.ID, "name": rel.Name, "modules": rel.Modules, "status": rel.Status}
		relModules := []map[string]any{}
		rows, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_relationship_modules WHERE relationship_id=? ORDER BY id`, rel.ID)
		if len(rows) == 0 {
			for _, rawID := range strings.Split(rel.Modules, ",") {
				moduleID := int64From(strings.TrimSpace(rawID))
				if moduleID > 0 {
					rows = append(rows, map[string]any{"relationship_id": rel.ID, "module_id": moduleID})
				}
			}
		}
		for _, row := range rows {
			moduleID := int64From(row["module_id"])
			module, mErr := db.GetModule(r.Context(), s.db, moduleID)
			if mErr != nil {
				continue
			}
			fields, _ := db.Fields(r.Context(), s.db, moduleID, "All")
			row["module"] = module
			row["modulefields"] = fields
			relModules = append(relModules, row)
		}
		relationship["relationshipmodule"] = relModules
		subpanel["relationship"] = relationship
	}
	if module, err := db.GetModule(r.Context(), s.db, int64From(subpanel["module_id"])); err == nil {
		subpanel["module"] = module
	}
	return subpanel, nil
}

func (s *Server) relationshipRecord(r *http.Request, relationshipID, recordID int64) map[string]any {
	rel, err := s.relationship(r, relationshipID)
	if err != nil {
		return nil
	}
	rows, err := s.db.QueryContext(r.Context(), `SELECT * FROM `+db.Q(rel.Name)+` WHERE id=? LIMIT 1`, recordID)
	if err != nil {
		return nil
	}
	item, _ := db.RowToMap(rows)
	return item
}

func (s *Server) firstRelationshipModuleID(r *http.Request, relationshipID int64) int64 {
	var id int64
	_ = s.db.QueryRowContext(r.Context(), `SELECT module_id FROM ice_relationship_modules WHERE relationship_id=? ORDER BY id LIMIT 1`, relationshipID).Scan(&id)
	if id > 0 {
		return id
	}
	var modules string
	_ = s.db.QueryRowContext(r.Context(), `SELECT modules FROM ice_relationships WHERE id=? LIMIT 1`, relationshipID).Scan(&modules)
	for _, part := range strings.Split(modules, ",") {
		if id = int64From(strings.TrimSpace(part)); id > 0 {
			return id
		}
	}
	return 0
}

func groupedNewRecords(input map[string]any) map[int64]map[string]any {
	out := map[int64]map[string]any{}
	for key, value := range input {
		parts := strings.SplitN(key, "__", 2)
		if len(parts) != 2 {
			continue
		}
		moduleID := int64From(parts[0])
		if moduleID == 0 {
			continue
		}
		if out[moduleID] == nil {
			out[moduleID] = map[string]any{}
		}
		out[moduleID][key] = value
	}
	return out
}

func selectedModuleRecords(input map[string]any) map[int64]int64 {
	out := map[int64]int64{}
	for key, value := range input {
		if strings.HasPrefix(key, "module_records[") && strings.HasSuffix(key, "]") {
			moduleID := int64From(strings.TrimSuffix(strings.TrimPrefix(key, "module_records["), "]"))
			if moduleID > 0 {
				out[moduleID] = int64From(value)
			}
		}
		if key == "module_records" {
			switch v := value.(type) {
			case map[string]any:
				for rawModuleID, rawRecordID := range v {
					out[int64From(rawModuleID)] = int64From(rawRecordID)
				}
			case map[string]string:
				for rawModuleID, rawRecordID := range v {
					out[int64From(rawModuleID)] = int64From(rawRecordID)
				}
			case map[int64]int64:
				for moduleID, recordID := range v {
					out[moduleID] = recordID
				}
			}
		}
	}
	for moduleID, recordID := range out {
		if moduleID == 0 || recordID == 0 {
			delete(out, moduleID)
		}
	}
	return out
}

func coerceValue(field db.Field, value any) any {
	switch field.InputType {
	case "checkbox":
		v := strings.ToLower(fmt.Sprint(value))
		return v == "1" || v == "true" || v == "on"
	case "date":
		if t, err := time.Parse("2006-01-02", fmt.Sprint(value)); err == nil {
			return t.Unix()
		}
	}
	return value
}

func isStringField(fields []db.Field, name string) bool {
	for _, field := range fields {
		if field.Name == name {
			return field.DataType == "string" || field.DataType == "text"
		}
	}
	return false
}

func status(err error) int {
	if err != nil {
		return http.StatusUnprocessableEntity
	}
	return http.StatusOK
}

func idsFrom(input map[string]any) []int64 {
	out := []int64{}
	for _, value := range input {
		switch v := value.(type) {
		case []any:
			for _, item := range v {
				out = append(out, int64From(item))
			}
		case []string:
			for _, item := range v {
				out = append(out, int64From(item))
			}
		default:
			if id := int64From(v); id > 0 {
				out = append(out, id)
			}
		}
	}
	return out
}

func countTable(r *http.Request, database *sql.DB, table string) int64 {
	if !db.ValidIdent(table) {
		return 0
	}
	var n int64
	_ = database.QueryRowContext(r.Context(), `SELECT COUNT(*) FROM `+db.Q(table)).Scan(&n)
	return n
}

func unixDate(ts int64) string {
	if ts <= 0 {
		return ""
	}
	return time.Unix(ts, 0).Format("2006-01-02 15:04")
}

func int64From(v any) int64 {
	switch t := v.(type) {
	case int64:
		return t
	case int:
		return int64(t)
	case float64:
		return int64(t)
	case string:
		n, _ := strconv.ParseInt(t, 10, 64)
		return n
	default:
		return 0
	}
}

func intFrom(v any, fallback int) int {
	n := int(int64From(v))
	if n < 1 {
		return fallback
	}
	return n
}

func first(values ...string) string {
	for _, value := range values {
		if value != "" {
			return value
		}
	}
	return ""
}

func firstAny(values ...any) any {
	for _, value := range values {
		if value != nil && fmt.Sprint(value) != "" {
			return value
		}
	}
	return nil
}

func firstMap(items []map[string]any) any {
	if len(items) == 0 {
		return nil
	}
	return items[0]
}

func queryAsMap(r *http.Request) map[string]string {
	out := map[string]string{}
	for key, values := range r.URL.Query() {
		if len(values) > 0 {
			out[key] = values[0]
		}
	}
	return out
}

func queryAsAnyMap(r *http.Request) map[string]any {
	out := map[string]any{}
	for key, values := range r.URL.Query() {
		if len(values) > 0 {
			out[key] = values[0]
		}
	}
	return out
}

func nullZero(v any) any {
	if int64From(v) == 0 {
		return nil
	}
	return v
}

func filepathClean(path string) string {
	return filepath.Clean(path)
}
