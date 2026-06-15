package web

import (
	"fmt"
	"net/http"
	"strconv"
	"strings"

	"iceburgcrm/goversion/internal/db"
)

func (s *Server) apiUser(w http.ResponseWriter, r *http.Request) {
	writeJSON(w, http.StatusOK, s.currentUser(r))
}

func (s *Server) dashboard(w http.ResponseWriter, r *http.Request) {
	s.render(w, r, "Dashboard", map[string]any{"datalets": s.activeDatalets(r)})
}

func (s *Server) modulesPage(w http.ResponseWriter, r *http.Request) {
	modules, _ := db.Modules(r.Context(), s.db, true)
	s.render(w, r, "Modules", map[string]any{"modules": modules, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Modules", "url": "", "svg": "settings"})})
}

func (s *Server) importPage(w http.ResponseWriter, r *http.Request) {
	modules, _ := db.Modules(r.Context(), s.db, true)
	s.render(w, r, "Import", map[string]any{
		"modules": modules,
		"data": map[string]any{
			"module_id":        first(r.URL.Query().Get("from_module_id"), r.URL.Query().Get("module_id")),
			"module_name":      r.URL.Query().Get("module_name"),
			"first_row_header": r.URL.Query().Get("first_row_header"),
		},
		"from_module_id": r.URL.Query().Get("from_module_id"),
		"breadcrumbs":    db.Breadcrumbs(map[string]string{"name": "Import", "url": "", "svg": "settings"}),
	})
}

func (s *Server) settings(w http.ResponseWriter, r *http.Request) {
	if r.Method == http.MethodPost {
		input := formMap(r)
		for key, value := range input {
			_, _ = s.db.ExecContext(r.Context(), `UPDATE ice_settings SET value=?,updated_at=NOW() WHERE name=?`, fmt.Sprint(value), key)
		}
		writeJSON(w, http.StatusOK, 1)
		return
	}
	themes, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_themes`)
	s.render(w, r, "Settings", map[string]any{"settings": 0, "themes": themes, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Settings", "url": "", "svg": "settings"})})
}

func (s *Server) rolePermission(w http.ResponseWriter, r *http.Request) {
	perms, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_permissions`)
	roles, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_roles`)
	modules, _ := db.Modules(r.Context(), s.db, true)
	s.render(w, r, "Admin/Permissions", map[string]any{"permissions": perms, "roles": roles, "modules": modules, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Permissions", "url": "", "svg": "settings"})})
}

func (s *Server) calendar(w http.ResponseWriter, r *http.Request) {
	user := s.currentUser(r)
	rows, err := s.db.QueryContext(r.Context(), `SELECT id,name,COALESCE(description,''),COALESCE(start_date,0),COALESCE(end_date,0) FROM meetings WHERE assigned_to=? ORDER BY start_date`, user.ID)
	events := []map[string]any{}
	if err == nil {
		defer rows.Close()
		for rows.Next() {
			var id int64
			var name, desc string
			var start, end int64
			_ = rows.Scan(&id, &name, &desc, &start, &end)
			events = append(events, map[string]any{"title": name, "time": map[string]any{"start": unixDate(start), "end": unixDate(end)}, "color": "yellow", "id": id, "isEditable": false, "description": desc})
		}
	}
	s.render(w, r, "Calendar", map[string]any{"events": events, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Calendar", "url": "", "svg": "settings"})})
}

func (s *Server) modulePage(w http.ResponseWriter, r *http.Request) {
	path := strings.TrimPrefix(r.URL.Path, "/module/")
	parts := strings.Split(strings.Trim(path, "/"), "/")
	if len(parts) == 0 || parts[0] == "" {
		http.NotFound(w, r)
		return
	}
	module, err := db.GetModuleByName(r.Context(), s.db, parts[0])
	if err != nil {
		http.NotFound(w, r)
		return
	}
	switch {
	case len(parts) == 1:
		s.moduleList(w, r, module)
	case len(parts) >= 2 && parts[1] == "add":
		s.moduleAdd(w, r, module, 0)
	case len(parts) >= 3 && parts[1] == "edit":
		id, _ := strconv.ParseInt(parts[2], 10, 64)
		s.moduleAdd(w, r, module, id)
	case len(parts) >= 3 && (parts[1] == "view" || parts[1] == "edit"):
		id, _ := strconv.ParseInt(parts[2], 10, 64)
		s.moduleView(w, r, module, parts[1], id)
	default:
		http.NotFound(w, r)
	}
}

func (s *Server) moduleList(w http.ResponseWriter, r *http.Request, module *db.Module) {
	if !s.can(r, module.ID, "read") {
		redirect(w, r, "/dashboard")
		return
	}
	records := s.search(r, map[string]any{"module_id": module.ID, "search_type": "module"}, true)
	allModules, _ := db.Modules(r.Context(), s.db, false)
	display, _ := db.Fields(r.Context(), s.db, module.ID, "Display")
	searchFields, _ := db.Fields(r.Context(), s.db, module.ID, "Search")
	orderFields, _ := db.Fields(r.Context(), s.db, module.ID, "OrderBy")
	relationships, _ := queryMaps(r.Context(), s.db, `SELECT rm.*,r.name,r.label FROM ice_relationship_modules rm JOIN ice_relationships r ON r.id=rm.relationship_id WHERE rm.module_id=?`, module.ID)
	s.render(w, r, "Module/List", map[string]any{
		"module":          module,
		"modules":         allModules,
		"field_data":      s.relatedFieldData(r, module.ID),
		"page":            records,
		"records":         records,
		"records_object":  records,
		"display_fields":  db.FieldMap(display),
		"search_fields":   db.FieldMap(searchFields),
		"order_by_fields": db.FieldMap(orderFields),
		"request":         queryAsMap(r),
		"permissions":     s.permissions(r, module.ID),
		"relationships":   relationships,
		"breadcrumbs":     db.Breadcrumbs(map[string]string{"name": "Search", "url": "", "svg": "settings"}),
	})
}

func (s *Server) moduleAdd(w http.ResponseWriter, r *http.Request, module *db.Module, recordID int64) {
	if !s.can(r, module.ID, "write") {
		redirect(w, r, "/dashboard")
		return
	}
	fields, _ := db.Fields(r.Context(), s.db, module.ID, "All")
	allModules, _ := db.Modules(r.Context(), s.db, false)
	relationships, _ := queryMaps(r.Context(), s.db, `SELECT rm.*,r.name,r.label FROM ice_relationship_modules rm JOIN ice_relationships r ON r.id=rm.relationship_id WHERE rm.module_id=?`, module.ID)
	var record any
	typ := "add"
	if recordID > 0 {
		record = s.getRecord(r, module, recordID)
		typ = "edit"
	}
	s.render(w, r, "Module/Add", map[string]any{
		"module":        module,
		"fields":        fields,
		"record":        record,
		"type":          typ,
		"permissions":   s.permissions(r, module.ID),
		"relationships": relationships,
		"breadcrumbs":   db.Breadcrumbs(map[string]string{"name": "Search " + module.Label, "url": "/module/" + module.Name, "svg": "settings"}, map[string]string{"name": strings.Title(typ), "url": "", "svg": "settings"}),
		"modules":       allModules,
	})
}

func (s *Server) moduleView(w http.ResponseWriter, r *http.Request, module *db.Module, action string, recordID int64) {
	perm := "read"
	if action != "view" {
		perm = "write"
	}
	if !s.can(r, module.ID, perm) {
		redirect(w, r, "/dashboard")
		return
	}
	record := s.getRecord(r, module, recordID)
	if record == nil {
		redirect(w, r, "/dashboard")
		return
	}
	fields, _ := db.Fields(r.Context(), s.db, module.ID, "Display")
	s.render(w, r, "Module/"+strings.Title(action), map[string]any{
		"custom_data":  map[string]any{},
		"module":       module,
		"record":       record,
		"next":         s.nextID(r, module, recordID, ">"),
		"previous":     s.nextID(r, module, recordID, "<"),
		"workflow":     s.workflowData(r, module.ID, recordID),
		"field_data":   s.relatedFieldData(r, module.ID),
		"subpanel_ids": s.subpanelIDs(r, module.ID),
		"subpanels":    []any{},
		"permissions":  s.permissions(r, module.ID),
		"fields":       db.FieldMap(fields),
		"breadcrumbs":  db.Breadcrumbs(map[string]string{"name": "Search", "url": "/module/" + module.Name, "svg": "settings"}, map[string]string{"name": strings.Title(action), "url": "", "svg": "settings"}),
	})
}

func (s *Server) data(w http.ResponseWriter, r *http.Request) {
	path := strings.Trim(strings.TrimPrefix(r.URL.Path, "/data"), "/")
	if isAdminDataPath(path) && !s.isAdmin(r) {
		if wantsJSON(r) {
			writeJSON(w, http.StatusForbidden, map[string]any{"error": "No Access"})
			return
		}
		redirect(w, r, "/dashboard")
		return
	}
	switch {
	case path == "permissions" || path == "permissions/save" || strings.HasPrefix(path, "builder/") || path == "settings" || path == "resetcrm" || strings.HasPrefix(path, "commands/run/") || path == "sendrequest" || path == "module":
		s.adminDataPath(w, r, path)
	case path == "save":
		s.saveRecord(w, r)
	case strings.HasPrefix(path, "download/"):
		s.download(w, r, path)
	case strings.HasPrefix(path, "delete/"):
		s.deleteRecord(w, r, path)
	case path == "search_data":
		input := formMap(r)
		if r.Method == http.MethodGet {
			input = queryAsAnyMap(r)
		}
		writeJSON(w, http.StatusOK, s.search(r, input, false))
	case strings.HasPrefix(path, "ai_assist/fields/"):
		s.aiAssistFields(w, r, strings.TrimPrefix(path, "ai_assist/fields/"))
	case path == "import":
		s.importData(w, r)
	case path == "subpanel/save":
		s.saveSubpanel(w, r)
	case strings.HasPrefix(path, "module/"):
		s.dataModuleRecord(w, r, path)
	case strings.HasPrefix(path, "search_fields/"):
		s.dataSearchFields(w, r, path)
	case path == "datalet":
		id, _ := strconv.ParseInt(r.URL.Query().Get("id"), 10, 64)
		writeJSON(w, http.StatusOK, s.dataletData(r, id))
	case path == "help":
		item, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_help WHERE slug=? LIMIT 1`, r.URL.Query().Get("slug"))
		if len(item) == 0 {
			writeJSON(w, http.StatusNotFound, map[string]any{"error": "Help content not found"})
			return
		}
		writeJSON(w, http.StatusOK, item[0])
	case strings.HasPrefix(path, "subpanel/"):
		id, _ := strconv.ParseInt(strings.TrimPrefix(path, "subpanel/"), 10, 64)
		writeJSON(w, http.StatusOK, s.subpanelData(r, id))
	case strings.HasPrefix(path, "related_fields/field_id/"):
		s.relatedFields(w, r, strings.TrimPrefix(path, "related_fields/field_id/"))
	case strings.HasPrefix(path, "related_field_name/field_id/"):
		s.relatedFieldName(w, r, path)
	case strings.HasPrefix(path, "connector/"), path == "connectors", strings.HasPrefix(path, "connectors/"), strings.HasPrefix(path, "endpoints"), strings.HasPrefix(path, "commands"), strings.HasPrefix(path, "workflow"):
		s.connectorData(w, r, path)
	default:
		http.NotFound(w, r)
	}
}

func isAdminDataPath(path string) bool {
	return path == "permissions" ||
		path == "permissions/save" ||
		strings.HasPrefix(path, "builder/") ||
		strings.HasPrefix(path, "commands/run/") ||
		path == "sendrequest" ||
		path == "module" ||
		strings.HasPrefix(path, "connector/") ||
		path == "connectors" ||
		strings.HasPrefix(path, "connectors/") ||
		strings.HasPrefix(path, "endpoints") ||
		strings.HasPrefix(path, "commands") ||
		strings.HasPrefix(path, "workflow")
}

func (s *Server) admin(w http.ResponseWriter, r *http.Request) {
	path := strings.Trim(strings.TrimPrefix(r.URL.Path, "/admin"), "/")
	if strings.HasPrefix(path, "connector/") {
		id := strings.TrimPrefix(path, "connector/")
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_connectors WHERE id=? LIMIT 1`, id)
		var connector any
		if len(items) > 0 {
			commands, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_connector_commands WHERE connector_id=? ORDER BY id`, id)
			items[0]["commands"] = commands
			connector = items[0]
		}
		s.render(w, r, "Admin/Connector", map[string]any{"connector": connector, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Connectors", "url": "/admin/connectors", "svg": "settings"})})
		return
	}
	switch path {
	case "", "modules":
		modules, _ := db.Modules(r.Context(), s.db, true)
		s.render(w, r, "Admin/Modules", map[string]any{"themes": modules, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Modules", "url": "", "svg": "settings"})})
	case "connectors":
		connectors, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_connectors ORDER BY type DESC`)
		s.render(w, r, "Admin/Connectors", map[string]any{"connectors": connectors, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Connectors", "url": "", "svg": "settings"})})
	case "data":
		s.render(w, r, "Admin/Data", map[string]any{"breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Data", "url": "", "svg": "settings"})})
	case "connector":
		s.render(w, r, "Admin/Connector", map[string]any{"connector": nil, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Connectors", "url": "/admin/connectors", "svg": "settings"})})
	case "scheduler":
		schedule, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_schedules WHERE user_id=? AND status=1`, s.currentUser(r).ID)
		s.render(w, r, "Admin/Schedule", map[string]any{"schedule": schedule, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Scheduler", "url": "", "svg": "settings"})})
	case "workflow":
		workflow, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_module_convertables ORDER BY level`)
		modules, _ := db.Modules(r.Context(), s.db, true)
		s.render(w, r, "Admin/Workflow", map[string]any{"workflow": workflow, "modules": modules})
	case "subpanels":
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_module_subpanels WHERE status=1`)
		s.render(w, r, "Admin/Subpanels", map[string]any{"subpanels": items, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Subpanels", "url": "", "svg": "settings"})})
	case "datalets":
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_datalets WHERE active=1`)
		s.render(w, r, "Admin/Datalets", map[string]any{"subpanels": items, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Datalets", "url": "", "svg": "settings"})})
	case "builder":
		modules, _ := db.Modules(r.Context(), s.db, false)
		datalets, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_datalets`)
		relationships, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_relationships`)
		s.render(w, r, "Admin/Builder", map[string]any{"modules": modules, "datalets": datalets, "relationships": relationships, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Builder", "url": "", "svg": "settings"})})
	case "permissions":
		perms, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_permissions`)
		roles, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_roles`)
		modules, _ := db.Modules(r.Context(), s.db, true)
		s.render(w, r, "Admin/Permissions", map[string]any{"permissions": perms, "roles": roles, "modules": modules, "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Admin", "url": "", "svg": "admin"}, map[string]string{"name": "Permissions", "url": "", "svg": "settings"})})
	default:
		http.NotFound(w, r)
	}
}

func (s *Server) adminIndex(w http.ResponseWriter, r *http.Request) {
	s.admin(w, r)
}

func (s *Server) adminData(w http.ResponseWriter, r *http.Request) {
	path := strings.Trim(strings.TrimPrefix(r.URL.Path, "/admin_data"), "/")
	s.adminDataPath(w, r, path)
}

func (s *Server) adminDataPath(w http.ResponseWriter, r *http.Request, path string) {
	switch {
	case path == "module":
		s.adminModuleData(w, r)
	case path == "permissions":
		items, _ := queryMaps(r.Context(), s.db, `SELECT * FROM ice_permissions`)
		writeJSON(w, http.StatusOK, items)
	case path == "permissions/save":
		input := formMap(r)
		col := "can_" + fmt.Sprint(input["type"])
		if !db.ValidIdent(col) {
			writeJSON(w, http.StatusBadRequest, map[string]any{"error": "invalid permission"})
			return
		}
		_, err := s.db.ExecContext(r.Context(), `UPDATE ice_permissions SET `+db.Q(col)+`=IF(`+db.Q(col)+`=1,0,1),updated_at=NOW() WHERE id=?`, input["id"])
		writeJSON(w, status(err), map[string]any{"success": err == nil})
	case strings.HasPrefix(path, "builder/"):
		s.builder(w, r, path)
	case path == "settings":
		s.settings(w, r)
	case path == "resetcrm":
		writeJSON(w, http.StatusOK, map[string]any{"status": s.resetCRM(r)})
	case strings.HasPrefix(path, "commands/run/"):
		s.runConnectorCommand(w, r, strings.TrimPrefix(path, "commands/run/"))
	case path == "sendrequest":
		s.sendEndpointRequest(w, r)
	default:
		http.NotFound(w, r)
	}
}

func (s *Server) apiCRM(w http.ResponseWriter, r *http.Request) {
	if r.URL.Path == "/api/crm/search" {
		writeJSON(w, http.StatusOK, s.search(r, queryAsAnyMap(r), false))
		return
	}
	switch r.Method {
	case http.MethodGet:
		if strings.HasPrefix(r.URL.Path, "/api/crm/") {
			id := int64From(strings.TrimPrefix(r.URL.Path, "/api/crm/"))
			if id > 0 {
				module, err := db.GetModule(r.Context(), s.db, id)
				if err != nil || !s.can(r, id, "export") {
					writeJSON(w, http.StatusUnprocessableEntity, map[string]any{"error": "No Access"})
					return
				}
				writeJSON(w, http.StatusOK, module)
				return
			}
		}
		writeJSON(w, http.StatusOK, s.search(r, queryAsAnyMap(r), false))
	case http.MethodPut, http.MethodPost:
		input := formMap(r)
		if strings.HasPrefix(r.URL.Path, "/api/crm/") {
			if moduleID := int64From(strings.TrimPrefix(r.URL.Path, "/api/crm/")); moduleID > 0 {
				input["module_id"] = moduleID
			}
		}
		s.saveRecordInput(w, r, input)
	case http.MethodDelete:
		parts := strings.Split(strings.TrimPrefix(r.URL.Path, "/api/crm/"), "/")
		if len(parts) >= 2 {
			s.deleteRecord(w, r, "delete/"+parts[0]+"/type/"+parts[1])
			return
		}
		http.NotFound(w, r)
	default:
		http.NotFound(w, r)
	}
}

func (s *Server) auditLog(w http.ResponseWriter, r *http.Request) {
	id, _ := strconv.ParseInt(strings.TrimPrefix(r.URL.Path, "/audit_log/"), 10, 64)
	module, _ := db.GetModule(r.Context(), s.db, id)
	logs, _ := queryMaps(r.Context(), s.db, `SELECT l.*,u.name AS user_name FROM ice_logs l LEFT JOIN ice_users u ON u.id=l.user_id WHERE l.module_id=?`, id)
	users, _ := queryMaps(r.Context(), s.db, `SELECT id,name,email,role_id FROM ice_users`)
	s.render(w, r, "Module/AuditLog", map[string]any{"logs": logs, "users": users, "module": module, "permissions": s.permissions(r, id), "breadcrumbs": db.Breadcrumbs(map[string]string{"name": "Search", "url": "/module/" + module.Name, "svg": "settings"}, map[string]string{"name": "Audit Log", "url": "", "svg": "settings"})})
}

func (s *Server) subpanelPage(w http.ResponseWriter, r *http.Request) {
	path := strings.Trim(strings.TrimPrefix(r.URL.Path, "/subpanel/"), "/")
	parts := strings.Split(path, "/")
	if len(parts) < 2 {
		http.NotFound(w, r)
		return
	}
	var subpanelID, recordID int64
	mode := "add"
	if parts[0] == "add" {
		subpanelID = int64From(parts[1])
	} else if len(parts) >= 3 && parts[1] == "edit" {
		subpanelID = int64From(parts[0])
		recordID = int64From(parts[2])
		mode = "edit"
	}
	subpanel, err := s.subpanelPayload(r, subpanelID)
	if err != nil {
		http.NotFound(w, r)
		return
	}
	if !s.can(r, int64From(subpanel["module_id"]), "write") {
		redirect(w, r, "/dashboard")
		return
	}
	fields := map[int64][]db.Field{}
	if rel, ok := subpanel["relationship"].(map[string]any); ok {
		if modules, ok := rel["relationshipmodule"].([]map[string]any); ok {
			for _, item := range modules {
				moduleID := int64From(item["module_id"])
				moduleFields, _ := db.Fields(r.Context(), s.db, moduleID, "All")
				fields[moduleID] = moduleFields
			}
		}
	}
	fromModuleID := int64From(r.URL.Query().Get("from_module"))
	fromModule, _ := db.GetModule(r.Context(), s.db, fromModuleID)
	var record any
	selected := map[int64]int64{}
	if recordID > 0 {
		record = s.relationshipRecord(r, int64From(subpanel["relationship_id"]), recordID)
		if rec, ok := record.(map[string]any); ok {
			for key, value := range rec {
				if strings.HasSuffix(key, "_id") {
					moduleName := strings.TrimSuffix(key, "_id")
					if module, err := db.GetModuleByName(r.Context(), s.db, moduleName); err == nil {
						selected[module.ID] = int64From(value)
					}
				}
			}
		}
	}
	s.render(w, r, "Subpanel/Add", map[string]any{
		"subpanel":         subpanel,
		"fields":           fields,
		"selected_records": selected,
		"record":           record,
		"from_module":      fromModule,
		"from_id":          int64From(r.URL.Query().Get("from_id")),
		"breadcrumbs":      db.Breadcrumbs(map[string]string{"name": strings.Title(mode), "url": "", "svg": "settings"}),
	})
}

func (s *Server) relationshipPage(w http.ResponseWriter, r *http.Request) {
	path := strings.Trim(strings.TrimPrefix(r.URL.Path, "/relationship/"), "/")
	parts := strings.Split(path, "/")
	if len(parts) < 2 || parts[0] == "" || parts[1] != "add" {
		http.NotFound(w, r)
		return
	}
	rel, err := s.relationshipByName(r, parts[0])
	if err != nil {
		http.NotFound(w, r)
		return
	}
	moduleID := s.firstRelationshipModuleID(r, rel.ID)
	module, err := db.GetModule(r.Context(), s.db, moduleID)
	if err != nil || !s.can(r, moduleID, "write") {
		redirect(w, r, "/dashboard")
		return
	}
	fields := map[string]db.Field{}
	for _, rawModuleID := range strings.Split(rel.Modules, ",") {
		moduleFields, _ := db.Fields(r.Context(), s.db, int64From(strings.TrimSpace(rawModuleID)), "All")
		for key, field := range db.FieldMap(moduleFields) {
			fields[key] = field
		}
	}
	allModules, _ := db.Modules(r.Context(), s.db, false)
	s.render(w, r, "Module/Add", map[string]any{
		"module":       module,
		"fields":       fields,
		"record":       nil,
		"type":         "add",
		"relationship": map[string]any{"id": rel.ID, "name": rel.Name, "modules": rel.Modules, "status": rel.Status},
		"permissions":  s.permissions(r, moduleID),
		"breadcrumbs":  db.Breadcrumbs(map[string]string{"name": "Search " + module.Label, "url": "/module/" + module.Name, "svg": "settings"}, map[string]string{"name": "Add", "url": "", "svg": "settings"}),
		"modules":      allModules,
	})
}

func (s *Server) lang(w http.ResponseWriter, r *http.Request) {
	locale := strings.TrimPrefix(r.URL.Path, "/lang/")
	path := s.resourceFile("lang", locale+".json")
	if _, err := http.Dir(".").Open(path); err != nil {
		path = s.resourceFile("lang", "en.json")
	}
	http.ServeFile(w, r, filepathClean(path))
}
