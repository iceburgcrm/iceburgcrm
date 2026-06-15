package db

import (
	"context"
	"database/sql"
	"fmt"
	"strings"
)

type Module struct {
	ID            int64  `json:"id"`
	Name          string `json:"name"`
	Label         string `json:"label"`
	Description   string `json:"description"`
	Status        int    `json:"status"`
	CreateTable   int    `json:"create_table"`
	Admin         int    `json:"admin"`
	PrimaryField  string `json:"primary_field"`
	Icon          string `json:"icon"`
	ModuleGroupID int64  `json:"module_group_id"`
}

type Field struct {
	ID              int64  `json:"id"`
	Name            string `json:"name"`
	Label           string `json:"label"`
	ModuleID        int64  `json:"module_id"`
	Validation      string `json:"validation"`
	InputType       string `json:"input_type"`
	DataType        string `json:"data_type"`
	FieldLength     int    `json:"field_length"`
	Required        int    `json:"required"`
	IsNullable      int    `json:"is_nullable"`
	DefaultValue    string `json:"default_value"`
	ReadOnly        int    `json:"read_only"`
	RelatedModuleID int64  `json:"related_module_id"`
	RelatedFieldID  string `json:"related_field_id"`
	RelatedValueID  string `json:"related_value_id"`
	DecimalPlaces   int    `json:"decimal_places"`
	Status          int    `json:"status"`
	SearchDisplay   int    `json:"search_display"`
	ListDisplay     int    `json:"list_display"`
	EditDisplay     int    `json:"edit_display"`
	ViewDisplay     int    `json:"view_display"`
	DisplayOrder    int    `json:"display_order"`
	SearchOrder     int    `json:"search_order"`
	ListOrder       int    `json:"list_order"`
	EditOrder       int    `json:"edit_order"`
	Description     string `json:"description"`
	Module          any    `json:"module,omitempty"`
	RelatedModule   any    `json:"related_module,omitempty"`
}

type User struct {
	ID        int64  `json:"id"`
	Name      string `json:"name"`
	Email     string `json:"email"`
	RoleID    int64  `json:"role_id"`
	Role      string `json:"role"`
	Password  string `json:"-"`
	Profile   string `json:"profile_pic,omitempty"`
	IceSlug   string `json:"ice_slug"`
	CreatedAt string `json:"created_at,omitempty"`
	UpdatedAt string `json:"updated_at,omitempty"`
}

func GetModuleByName(ctx context.Context, db *sql.DB, name string) (*Module, error) {
	rows, err := db.QueryContext(ctx, `SELECT id,name,label,description,status,create_table,admin,primary_field,icon,module_group_id FROM ice_modules WHERE name=? LIMIT 1`, name)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	if !rows.Next() {
		return nil, sql.ErrNoRows
	}
	return scanModule(rows)
}

func GetModule(ctx context.Context, db *sql.DB, id int64) (*Module, error) {
	rows, err := db.QueryContext(ctx, `SELECT id,name,label,description,status,create_table,admin,primary_field,icon,module_group_id FROM ice_modules WHERE id=? LIMIT 1`, id)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	if !rows.Next() {
		return nil, sql.ErrNoRows
	}
	return scanModule(rows)
}

func scanModule(rows interface{ Scan(...any) error }) (*Module, error) {
	m := &Module{}
	err := rows.Scan(&m.ID, &m.Name, &m.Label, &m.Description, &m.Status, &m.CreateTable, &m.Admin, &m.PrimaryField, &m.Icon, &m.ModuleGroupID)
	if m.PrimaryField == "" {
		m.PrimaryField = "id"
	}
	return m, err
}

func Modules(ctx context.Context, db *sql.DB, activeOnly bool) ([]map[string]any, error) {
	query := `SELECT * FROM ice_modules`
	if activeOnly {
		query += ` WHERE status=1`
	}
	query += ` ORDER BY view_order,id`
	rows, err := db.QueryContext(ctx, query)
	if err != nil {
		return nil, err
	}
	return RowsToMaps(rows)
}

func Fields(ctx context.Context, db *sql.DB, moduleID int64, mode string) ([]Field, error) {
	query := `SELECT id,name,label,module_id,COALESCE(validation,''),input_type,data_type,COALESCE(field_length,0),required,is_nullable,default_value,read_only,related_module_id,related_field_id,related_value_id,COALESCE(decimal_places,0),status,search_display,list_display,edit_display,view_display,display_order,search_order,list_order,edit_order,COALESCE(description,'') FROM ice_fields WHERE module_id=? AND status=1`
	switch mode {
	case "Search":
		query += ` AND search_display=1 ORDER BY search_order,id`
	case "Display", "List":
		query += ` AND list_display=1 ORDER BY display_order,id`
	case "Edit":
		query += ` AND edit_display=1 ORDER BY edit_order,id`
	default:
		query += ` ORDER BY id`
	}
	rows, err := db.QueryContext(ctx, query, moduleID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	out := []Field{}
	for rows.Next() {
		f := Field{}
		err := rows.Scan(&f.ID, &f.Name, &f.Label, &f.ModuleID, &f.Validation, &f.InputType, &f.DataType, &f.FieldLength, &f.Required, &f.IsNullable, &f.DefaultValue, &f.ReadOnly, &f.RelatedModuleID, &f.RelatedFieldID, &f.RelatedValueID, &f.DecimalPlaces, &f.Status, &f.SearchDisplay, &f.ListDisplay, &f.EditDisplay, &f.ViewDisplay, &f.DisplayOrder, &f.SearchOrder, &f.ListOrder, &f.EditOrder, &f.Description)
		if err != nil {
			return nil, err
		}
		out = append(out, f)
	}
	return out, rows.Err()
}

func FieldMap(fields []Field) map[string]Field {
	out := map[string]Field{}
	for _, field := range fields {
		out[fmt.Sprintf("%d__%s", field.ID, field.Name)] = field
	}
	return out
}

func Setting(ctx context.Context, db *sql.DB, key string) string {
	var value sql.NullString
	_ = db.QueryRowContext(ctx, `SELECT value FROM ice_settings WHERE name=? LIMIT 1`, key).Scan(&value)
	if value.Valid && value.String != "" {
		return value.String
	}
	if key == "theme" {
		return "light"
	}
	if key == "search_per_page" || key == "submodule_search_per_page" {
		return "10"
	}
	if key == "max_export_records" {
		return "1000"
	}
	return ""
}

func Breadcrumbs(items ...map[string]string) []map[string]string {
	out := []map[string]string{{"name": "Home", "url": "/dashboard", "svg": "home"}}
	out = append(out, items...)
	return out
}

func SafeColumnList(fields []Field) []string {
	cols := []string{}
	for _, field := range fields {
		if ValidIdent(field.Name) {
			cols = append(cols, field.Name)
		}
	}
	return cols
}

func Placeholders(n int) string {
	return strings.TrimRight(strings.Repeat("?,", n), ",")
}
