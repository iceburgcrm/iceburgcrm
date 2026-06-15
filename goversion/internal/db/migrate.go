package db

import (
	"context"
	"database/sql"
	"fmt"
	"strings"
)

func Migrate(ctx context.Context, database *sql.DB) error {
	err := ExecMany(ctx, database, []string{
		`CREATE TABLE IF NOT EXISTS ice_roles (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(245) NOT NULL DEFAULT '', label VARCHAR(245) NOT NULL DEFAULT '', status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_users (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL UNIQUE, email_verified_at TIMESTAMP NULL, password VARCHAR(255) NOT NULL, profile_pic MEDIUMTEXT NULL, role_id INT NOT NULL DEFAULT 2, ice_slug VARCHAR(255) NOT NULL DEFAULT '', remember_token VARCHAR(100) NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_password_resets (email VARCHAR(255) NOT NULL, token VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL, INDEX(email))`,
		`CREATE TABLE IF NOT EXISTS ice_module_groups (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL DEFAULT '', label VARCHAR(245) NOT NULL DEFAULT '', status INT NOT NULL DEFAULT 1, view_order INT NOT NULL DEFAULT 0, icon VARCHAR(128) NOT NULL DEFAULT 'CircleStackIcon', created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_modules (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL DEFAULT '', label VARCHAR(255) NOT NULL DEFAULT '', description VARCHAR(245) NOT NULL DEFAULT '', status INT NOT NULL DEFAULT 1, faker_seed INT NOT NULL DEFAULT 1, create_table INT NOT NULL DEFAULT 1, view_order INT NOT NULL DEFAULT 0, admin INT NOT NULL DEFAULT 0, parent_id INT NOT NULL DEFAULT 0, ` + Q("primary") + ` INT NOT NULL DEFAULT 0, primary_field VARCHAR(64) NOT NULL DEFAULT 'id', icon VARCHAR(128) NOT NULL DEFAULT 'CircleStackIcon', module_group_id INT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, UNIQUE KEY ice_modules_name_unique(name))`,
		`CREATE TABLE IF NOT EXISTS ice_fields (name VARCHAR(245) NOT NULL, label VARCHAR(245) NOT NULL, module_id INT NOT NULL, validation VARCHAR(245) NULL, input_type VARCHAR(245) NOT NULL DEFAULT 'text', data_type VARCHAR(100) NOT NULL, field_length INT NULL, required INT NOT NULL DEFAULT 0, is_nullable TINYINT NOT NULL DEFAULT 0, default_value VARCHAR(245) NOT NULL DEFAULT '', read_only TINYINT NOT NULL DEFAULT 0, related_module_id INT NOT NULL DEFAULT 0, related_field_id VARCHAR(255) NOT NULL DEFAULT '', related_value_id VARCHAR(255) NOT NULL DEFAULT '', decimal_places INT NULL, status TINYINT NOT NULL DEFAULT 1, search_display TINYINT NOT NULL DEFAULT 1, list_display TINYINT NOT NULL DEFAULT 1, edit_display TINYINT NOT NULL DEFAULT 1, view_display TINYINT NOT NULL DEFAULT 1, display_order INT NOT NULL DEFAULT 9999, search_order INT NOT NULL DEFAULT 9999, list_order INT NOT NULL DEFAULT 9999, edit_order INT NOT NULL DEFAULT 9999, description TEXT NULL, id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, INDEX(module_id))`,
		`CREATE TABLE IF NOT EXISTS ice_relationships (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(245) NOT NULL DEFAULT '', label VARCHAR(245) NOT NULL DEFAULT '', modules VARCHAR(245) NOT NULL DEFAULT '', status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_relationship_modules (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, relationship_id INT NOT NULL, module_id INT NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL, INDEX(relationship_id), INDEX(module_id))`,
		`CREATE TABLE IF NOT EXISTS ice_module_subpanels (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(245) NOT NULL DEFAULT '', label VARCHAR(245) NOT NULL DEFAULT '', module_id INT NOT NULL DEFAULT 0, relationship_id INT NOT NULL DEFAULT 0, subpanel_fields TEXT NULL, list_order_column VARCHAR(128) NOT NULL DEFAULT 'id', list_order VARCHAR(8) NOT NULL DEFAULT 'asc', list_size INT NOT NULL DEFAULT 10, status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_subpanel_fields (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, field_id INT NOT NULL, subpanel_id INT NOT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_permissions (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, role_id INT NOT NULL, module_id INT NOT NULL, can_read TINYINT NOT NULL DEFAULT 1, can_write TINYINT NOT NULL DEFAULT 1, can_import TINYINT NOT NULL DEFAULT 1, can_export TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_logs (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL DEFAULT 0, type VARCHAR(64) NOT NULL DEFAULT '', message TEXT NULL, module_id INT NOT NULL DEFAULT 0, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_settings (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(245) NOT NULL UNIQUE, value MEDIUMTEXT NULL, additional_data MEDIUMTEXT NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_themes (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(245) NOT NULL DEFAULT '', value VARCHAR(245) NOT NULL DEFAULT '', status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_datalet_types (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(245) NOT NULL DEFAULT '', label VARCHAR(245) NOT NULL DEFAULT '', component VARCHAR(245) NOT NULL DEFAULT '', status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_datalets (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(245) NOT NULL DEFAULT '', label VARCHAR(245) NOT NULL DEFAULT '', type INT NOT NULL DEFAULT 1, module_id INT NOT NULL DEFAULT 0, field_id INT NOT NULL DEFAULT 0, relationship_id INT NOT NULL DEFAULT 0, role_id INT NOT NULL DEFAULT 0, active TINYINT NOT NULL DEFAULT 1, display_order INT NOT NULL DEFAULT 9999, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_module_convertables (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, primary_module_id INT NOT NULL DEFAULT 0, module_id INT NOT NULL DEFAULT 0, level INT NOT NULL DEFAULT 0, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_work_flow_data (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, from_id INT NOT NULL DEFAULT 0, from_module_id INT NOT NULL DEFAULT 0, to_id INT NOT NULL DEFAULT 0, to_module_id INT NOT NULL DEFAULT 0, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_connectors (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, name VARCHAR(245) NOT NULL DEFAULT '', type VARCHAR(64) NOT NULL DEFAULT '', auth_type VARCHAR(64) NOT NULL DEFAULT '', auth_key TEXT NULL, base_url TEXT NULL, token_url TEXT NULL, client_id TEXT NULL, client_secret TEXT NULL, username VARCHAR(245) NULL, password VARCHAR(245) NULL, access_token TEXT NULL, refresh_token TEXT NULL, token_expires_at TIMESTAMP NULL, class VARCHAR(245) NULL, status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_connector_commands (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, connector_id INT NOT NULL DEFAULT 0, name VARCHAR(245) NOT NULL DEFAULT '', method_name VARCHAR(245) NOT NULL DEFAULT '', description TEXT NULL, endpoint_id INT NULL, class_name VARCHAR(245) NULL, last_run_data MEDIUMTEXT NULL, last_run_status VARCHAR(64) NULL, last_run_message TEXT NULL, last_updated TIMESTAMP NULL, last_output MEDIUMTEXT NULL, status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_endpoints (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, connector_id INT NULL, name VARCHAR(245) NOT NULL DEFAULT '', endpoint TEXT NULL, request_type VARCHAR(16) NOT NULL DEFAULT 'GET', class_name VARCHAR(245) NULL, params TEXT NULL, headers TEXT NULL, status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_schedules (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL DEFAULT 0, name VARCHAR(245) NOT NULL DEFAULT '', status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_personal_access_tokens (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, tokenable_type VARCHAR(255) NOT NULL, tokenable_id BIGINT UNSIGNED NOT NULL, name VARCHAR(255) NOT NULL, token VARCHAR(64) NOT NULL UNIQUE, abilities TEXT NULL, last_used_at TIMESTAMP NULL, expires_at TIMESTAMP NULL, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
		`CREATE TABLE IF NOT EXISTS ice_help (id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, slug VARCHAR(245) NOT NULL UNIQUE, title VARCHAR(245) NOT NULL DEFAULT '', content MEDIUMTEXT NULL, status TINYINT NOT NULL DEFAULT 1, created_at TIMESTAMP NULL, updated_at TIMESTAMP NULL)`,
	})
	if err != nil {
		return err
	}
	return addCompatibilityColumns(ctx, database)
}

func addCompatibilityColumns(ctx context.Context, database *sql.DB) error {
	columns := []struct {
		table string
		name  string
		def   string
	}{
		{"ice_roles", "status", "TINYINT NOT NULL DEFAULT 1"},
		{"ice_roles", "ice_slug", "VARCHAR(255) NOT NULL DEFAULT ''"},
		{"ice_module_groups", "status", "TINYINT NOT NULL DEFAULT 1"},
		{"ice_module_groups", "icon", "VARCHAR(128) NOT NULL DEFAULT 'CircleStackIcon'"},
		{"ice_module_groups", "created_at", "TIMESTAMP NULL"},
		{"ice_module_groups", "updated_at", "TIMESTAMP NULL"},
		{"ice_themes", "value", "VARCHAR(245) NOT NULL DEFAULT ''"},
		{"ice_themes", "ice_slug", "VARCHAR(255) NOT NULL DEFAULT ''"},
		{"ice_themes", "status", "TINYINT NOT NULL DEFAULT 1"},
		{"ice_themes", "created_at", "TIMESTAMP NULL"},
		{"ice_themes", "updated_at", "TIMESTAMP NULL"},
		{"ice_connector_commands", "connector_id", "INT NOT NULL DEFAULT 0"},
		{"ice_connector_commands", "status", "TINYINT NOT NULL DEFAULT 1"},
		{"ice_connector_commands", "last_updated", "TIMESTAMP NULL"},
		{"ice_connector_commands", "last_output", "MEDIUMTEXT NULL"},
		{"ice_endpoints", "class_name", "VARCHAR(245) NULL"},
		{"ice_connectors", "class", "VARCHAR(245) NULL"},
		{"ice_datalets", "size", "INT NOT NULL DEFAULT 6"},
		{"ice_relationships", "label", "VARCHAR(245) NOT NULL DEFAULT ''"},
		{"ice_relationships", "related_field_types", "VARCHAR(245) NOT NULL DEFAULT 'integer,integer'"},
		{"ice_module_subpanels", "list_order_column", "VARCHAR(128) NOT NULL DEFAULT 'id'"},
		{"ice_module_subpanels", "list_order", "VARCHAR(8) NOT NULL DEFAULT 'asc'"},
		{"ice_module_subpanels", "list_size", "INT NOT NULL DEFAULT 10"},
	}
	for _, col := range columns {
		var exists int
		err := database.QueryRowContext(ctx, `SELECT COUNT(*) FROM information_schema.columns WHERE table_schema=DATABASE() AND table_name=? AND column_name=?`, col.table, col.name).Scan(&exists)
		if err != nil {
			return err
		}
		if exists == 0 {
			if _, err := database.ExecContext(ctx, `ALTER TABLE `+Q(col.table)+` ADD COLUMN `+Q(col.name)+` `+col.def); err != nil {
				return err
			}
		}
	}
	return nil
}

func Seed(ctx context.Context, database *sql.DB) error {
	adminHash, err := HashPassword("password")
	if err != nil {
		return err
	}
	statements := []string{
		`INSERT IGNORE INTO ice_roles (id,name,ice_slug,status,created_at,updated_at) VALUES (1,'Admin','admin',1,NOW(),NOW()),(2,'User','user',1,NOW(),NOW())`,
		`INSERT IGNORE INTO ice_settings (name,value,created_at,updated_at) VALUES ('title','Iceburg CRM',NOW(),NOW()),('description','Open source CRM',NOW(),NOW()),('theme','light',NOW(),NOW()),('language','en',NOW(),NOW()),('search_per_page','10',NOW(),NOW()),('submodule_search_per_page','10',NOW(),NOW()),('max_export_records','1000',NOW(),NOW()),('help','1',NOW(),NOW())`,
		`INSERT IGNORE INTO ice_themes (name,value,status,created_at,updated_at) VALUES ('light','light',1,NOW(),NOW()),('dark','dark',1,NOW(),NOW()),('corporate','corporate',1,NOW(),NOW()),('business','business',1,NOW(),NOW())`,
		fmt.Sprintf(`INSERT IGNORE INTO ice_users (id,name,email,email_verified_at,password,role_id,ice_slug,created_at,updated_at) VALUES (1,'Admin','admin@example.com',NOW(),'%s',1,'%s',NOW(),NOW())`, adminHash, RandomSlug(20)),
	}
	if err := ExecMany(ctx, database, statements); err != nil {
		return err
	}
	if err := seedDefaultCatalog(ctx, database); err != nil {
		return err
	}
	if err := seedDefaultFields(ctx, database); err != nil {
		return err
	}
	if err := seedDefaultRelationships(ctx, database); err != nil {
		return err
	}
	if err := seedDefaultSubpanels(ctx, database); err != nil {
		return err
	}
	if err := seedWorkflow(ctx, database); err != nil {
		return err
	}
	_, err = database.ExecContext(ctx, `INSERT IGNORE INTO ice_permissions (role_id,module_id,can_read,can_write,can_import,can_export,created_at,updated_at) SELECT 1,id,1,1,1,1,NOW(),NOW() FROM ice_modules`)
	return err
}

type seedModule struct {
	Name        string
	Label       string
	Description string
	GroupID     int
	Icon        string
	Admin       int
}

func seedDefaultCatalog(ctx context.Context, database *sql.DB) error {
	groups := []struct {
		ID        int
		Name      string
		Label     string
		ViewOrder int
		Icon      string
	}{
		{1, "companies", "Companies", 0, "BuildingOffice2Icon"},
		{2, "marketing", "Marketing", 1, "MegaphoneIcon"},
		{3, "sales", "Sales", 2, "CurrencyDollarIcon"},
		{4, "communications", "Communications", 3, "ChatBubbleLeftRightIcon"},
		{5, "more", "More", 4, "EllipsisHorizontalIcon"},
		{6, "admin", "Admin", 99, "Cog6ToothIcon"},
	}
	for _, group := range groups {
		_, err := database.ExecContext(ctx, `INSERT INTO ice_module_groups (id,name,label,status,view_order,icon,created_at,updated_at) VALUES (?,?,?,?,?,?,NOW(),NOW()) ON DUPLICATE KEY UPDATE name=VALUES(name),label=VALUES(label),status=VALUES(status),view_order=VALUES(view_order),icon=VALUES(icon),updated_at=NOW()`,
			group.ID, group.Name, group.Label, 1, group.ViewOrder, group.Icon)
		if err != nil {
			return err
		}
	}
	modules := []seedModule{
		{"accounts", "Accounts", "Account module", 1, "BuildingOffice2Icon", 0},
		{"contacts", "Contacts", "Contact module", 1, "UserIcon", 0},
		{"contracts", "Contracts", "Contract module", 1, "BookOpenIcon", 0},
		{"lineitems", "Line Items", "Line Items", 3, "Bars4Icon", 0},
		{"leads", "Leads", "Lead module", 2, "UsersIcon", 0},
		{"opportunities", "Opportunities", "Opportunity module", 2, "LightBulbIcon", 0},
		{"meetings", "Meetings", "Meetings module", 4, "MegaphoneIcon", 0},
		{"cases", "Cases", "Cases module", 2, "InboxStackIcon", 0},
		{"campaigns", "Campaigns", "Campaign module", 2, "ArrowRightOnRectangleIcon", 0},
		{"invoices", "Invoices", "Invoice module", 3, "CalculatorIcon", 0},
		{"quotes", "Quotes", "Quotes module", 3, "QueueListIcon", 0},
		{"orders", "Orders", "Order module", 3, "PencilSquareIcon", 0},
		{"documents", "Documents", "Document module", 5, "DocumentIcon", 0},
		{"notes", "Notes", "Notes module", 5, "PencilIcon", 0},
		{"groups", "Groups", "Groups module", 5, "UserGroupIcon", 0},
		{"projects", "Projects", "Projects module", 5, "RectangleGroupIcon", 0},
		{"tasks", "Tasks", "Tasks module", 4, "SparklesIcon", 0},
		{"activities", "Activities", "Activities module", 4, "CalendarDaysIcon", 0},
		{"products", "Products", "Products module", 5, "ArchiveBoxIcon", 0},
		{"ice_users", "Users", "Users", 6, "UserCircleIcon", 1},
		{"ice_modules", "Modules", "Module builder metadata", 6, "CircleStackIcon", 1},
		{"ice_fields", "Fields", "Field builder metadata", 6, "ListBulletIcon", 1},
		{"ice_module_subpanels", "Subpanels", "Subpanel metadata", 6, "RectangleGroupIcon", 1},
		{"ice_datalets", "Datalets", "Dashboard datalets", 6, "ChartBarIcon", 1},
		{"ice_roles", "Roles", "Roles", 6, "ShieldCheckIcon", 1},
		{"ice_themes", "Themes", "Themes", 6, "SwatchIcon", 1},
		{"ice_relationships", "Relationships", "Relationship metadata", 6, "LinkIcon", 1},
		{"document_types", "Document Types", "Document Types", 6, "DocumentDuplicateIcon", 1},
		{"document_status", "Document Status", "Document Status", 6, "DocumentCheckIcon", 1},
		{"countries", "Countries", "Countries", 6, "GlobeAltIcon", 1},
		{"states", "States", "States", 6, "GlobeAmericasIcon", 1},
		{"account_status", "Account Status", "Account Status", 6, "TagIcon", 1},
		{"contract_status", "Contract Status", "Contract Status", 6, "TagIcon", 1},
		{"currency", "Currency", "Currency", 6, "CurrencyPoundIcon", 1},
		{"contract_types", "Contract Types", "Contract Types", 6, "TagIcon", 1},
		{"discount_types", "Discount Types", "Discount Types", 6, "TagIcon", 1},
		{"opportunity_types", "Opportunity Types", "Opportunity Types", 6, "TagIcon", 1},
		{"opportunity_status", "Opportunity Status", "Opportunity Status", 6, "TagIcon", 1},
		{"lead_types", "Lead Types", "Lead Types", 6, "TagIcon", 1},
		{"lead_sources", "Lead Sources", "Lead Sources", 6, "TagIcon", 1},
		{"lead_status", "Lead Status", "Lead Status", 6, "TagIcon", 1},
		{"meeting_status", "Meeting Status", "Meeting Status", 6, "TagIcon", 1},
		{"meeting_types", "Meeting Types", "Meeting Types", 6, "PhoneIcon", 1},
		{"campaign_status", "Campaign Status", "Campaign Status", 6, "TagIcon", 1},
		{"campaign_types", "Campaign Types", "Campaign Types", 6, "TagIcon", 1},
		{"task_status", "Task Status", "Task Status", 6, "TagIcon", 1},
		{"task_types", "Task Types", "Task Types", 6, "TagIcon", 1},
		{"case_status", "Case Status", "Case Status", 6, "TagIcon", 1},
		{"case_priorities", "Case Priorities", "Case Priorities", 6, "TagIcon", 1},
		{"project_priorities", "Project Priorities", "Project Priorities", 6, "TagIcon", 1},
		{"case_types", "Case Types", "Case Types", 6, "TagIcon", 1},
		{"project_status", "Project Status", "Project Status", 6, "TagIcon", 1},
		{"project_types", "Project Types", "Project Types", 6, "TagIcon", 1},
		{"quote_status", "Quote Status", "Quote Status", 6, "TagIcon", 1},
		{"invoice_status", "Invoice Status", "Invoice Status", 6, "TagIcon", 1},
		{"task_priorities", "Task Priorities", "Task Priorities", 6, "TagIcon", 1},
		{"group_types", "Group Types", "Group Types", 6, "TagIcon", 1},
	}
	for order, module := range modules {
		createTable := 1
		if strings.HasPrefix(module.Name, "ice_") {
			createTable = 0
		}
		_, err := database.ExecContext(ctx, `INSERT INTO ice_modules (name,label,description,status,create_table,view_order,admin,parent_id,`+Q("primary")+`,primary_field,icon,module_group_id,created_at,updated_at)
			VALUES (?,?,?,?,?,?,?,?,?,'id',?,?,NOW(),NOW())
			ON DUPLICATE KEY UPDATE label=VALUES(label),description=VALUES(description),status=VALUES(status),create_table=VALUES(create_table),view_order=VALUES(view_order),admin=VALUES(admin),`+Q("primary")+`=VALUES(`+Q("primary")+`),icon=VALUES(icon),module_group_id=VALUES(module_group_id),updated_at=NOW()`,
			module.Name, module.Label, module.Description, 1, createTable, order, module.Admin, 0, 1, module.Icon, module.GroupID)
		if err != nil {
			return err
		}
	}
	return nil
}

func seedDefaultFields(ctx context.Context, database *sql.DB) error {
	rows, err := database.QueryContext(ctx, `SELECT id,name FROM ice_modules WHERE create_table=1`)
	if err != nil {
		return err
	}
	defer rows.Close()
	for rows.Next() {
		var id int64
		var name string
		if err := rows.Scan(&id, &name); err != nil {
			return err
		}
		fields := seedFieldsForModule(name)
		for i, field := range fields {
			_, err := database.ExecContext(ctx, `INSERT INTO ice_fields (name,label,module_id,input_type,data_type,field_length,is_nullable,status,display_order,search_order,list_order,edit_order,created_at,updated_at)
				SELECT ?,?,?,?, ?, ?,1,1,?,?,?, ?,NOW(),NOW() WHERE NOT EXISTS (SELECT 1 FROM ice_fields WHERE module_id=? AND name=?)`,
				field.Name, field.Label, id, field.InputType, field.DataType, field.Length, i+1, i+1, i+1, i+1, id, field.Name)
			if err != nil {
				return err
			}
		}
	}
	return rows.Err()
}

type seedField struct {
	Name      string
	Label     string
	InputType string
	DataType  string
	Length    int
}

func seedFieldsForModule(module string) []seedField {
	fields := []seedField{
		{"name", "Name", "text", "string", 245},
		{"description", "Description", "text", "string", 245},
	}
	if strings.HasPrefix(module, "ice_") || strings.HasSuffix(module, "_status") || strings.HasSuffix(module, "_types") || strings.HasSuffix(module, "_priorities") || module == "countries" || module == "states" || module == "currency" || module == "lead_sources" || module == "products" {
		return fields
	}
	fields = append(fields, seedField{"assigned_to", "Assigned To", "number", "bigInteger", 0})
	switch module {
	case "contacts", "leads":
		fields = append(fields,
			seedField{"first_name", "First Name", "text", "string", 245},
			seedField{"last_name", "Last Name", "text", "string", 245},
			seedField{"email", "Email", "email", "string", 245},
			seedField{"phone", "Phone", "text", "string", 64},
			seedField{"profile_pic", "Image", "image", "mediumText", 0},
			seedField{"status", "Status", "number", "bigInteger", 0},
		)
	case "accounts":
		fields = append(fields,
			seedField{"website", "Website", "text", "string", 245},
			seedField{"phone", "Phone", "text", "string", 64},
			seedField{"status", "Status", "number", "bigInteger", 0},
			seedField{"billing_address", "Billing Address", "text", "string", 245},
			seedField{"billing_city", "Billing City", "text", "string", 245},
			seedField{"billing_state", "Billing State", "number", "bigInteger", 0},
			seedField{"billing_country", "Billing Country", "number", "bigInteger", 0},
		)
	case "meetings":
		fields = append(fields,
			seedField{"start_date", "Start Date", "date", "bigInteger", 0},
			seedField{"end_date", "End Date", "date", "bigInteger", 0},
			seedField{"status", "Status", "number", "bigInteger", 0},
			seedField{"type", "Type", "number", "bigInteger", 0},
		)
	case "opportunities", "quotes", "invoices", "orders", "contracts":
		fields = append(fields,
			seedField{"status", "Status", "number", "bigInteger", 0},
			seedField{"currency", "Currency", "number", "bigInteger", 0},
			seedField{"amount", "Amount", "number", "decimal", 12},
			seedField{"total", "Total", "number", "decimal", 12},
			seedField{"expire_date", "Expire Date", "date", "bigInteger", 0},
		)
	case "cases", "tasks", "projects", "activities":
		fields = append(fields,
			seedField{"subject", "Subject", "text", "string", 245},
			seedField{"status", "Status", "number", "bigInteger", 0},
			seedField{"priority", "Priority", "number", "bigInteger", 0},
			seedField{"type", "Type", "number", "bigInteger", 0},
			seedField{"due_date", "Due Date", "date", "bigInteger", 0},
		)
	case "campaigns":
		fields = append(fields,
			seedField{"status", "Status", "number", "bigInteger", 0},
			seedField{"budget", "Budget", "number", "decimal", 12},
			seedField{"actual", "Actual", "number", "decimal", 12},
			seedField{"campaign_type", "Campaign Type", "number", "bigInteger", 0},
		)
	case "documents":
		fields = append(fields,
			seedField{"file_link", "Link Url", "text", "string", 245},
			seedField{"document_type", "Document Type", "number", "bigInteger", 0},
			seedField{"document_status", "Document Status", "number", "bigInteger", 0},
			seedField{"expire_date", "Expire Date", "date", "bigInteger", 0},
		)
	case "lineitems":
		fields = append(fields,
			seedField{"quantity", "Quantity", "number", "integer", 0},
			seedField{"price", "Price", "number", "decimal", 12},
			seedField{"total", "Total", "number", "decimal", 12},
		)
	}
	return fields
}

func seedDefaultRelationships(ctx context.Context, database *sql.DB) error {
	relationships := []struct {
		Name    string
		Modules []string
	}{
		{"accounts_contacts", []string{"accounts", "contacts"}},
		{"accounts_opportunities", []string{"accounts", "opportunities"}},
		{"leads_accounts_opportunities", []string{"leads", "accounts", "opportunities"}},
		{"accounts_cases", []string{"accounts", "cases"}},
		{"accounts_contracts", []string{"accounts", "contracts"}},
		{"accounts_meetings", []string{"accounts", "meetings"}},
		{"opportunities_contacts", []string{"opportunities", "contacts"}},
		{"opportunities_cases", []string{"opportunities", "cases"}},
		{"opportunities_contracts", []string{"opportunities", "contracts"}},
		{"opportunities_meetings", []string{"opportunities", "meetings"}},
		{"contracts_lineitems", []string{"contracts", "lineitems"}},
		{"users_tasks", []string{"ice_users", "tasks"}},
		{"users_meetings", []string{"ice_users", "meetings"}},
		{"campaigns_leads", []string{"campaigns", "leads"}},
		{"campaigns_contacts", []string{"campaigns", "contacts"}},
		{"projects_tasks", []string{"projects", "tasks"}},
		{"projects_activities", []string{"projects", "activities"}},
		{"documents_accounts", []string{"documents", "accounts"}},
		{"documents_contacts", []string{"documents", "contacts"}},
	}
	for _, rel := range relationships {
		ids := []string{}
		for _, name := range rel.Modules {
			id, err := moduleIDByName(ctx, database, name)
			if err != nil || id == 0 {
				continue
			}
			ids = append(ids, fmt.Sprint(id))
		}
		if len(ids) < 2 {
			continue
		}
		fieldTypes := strings.TrimRight(strings.Repeat("integer,", len(ids)), ",")
		_, err := database.ExecContext(ctx, `UPDATE ice_relationships SET label=?,modules=?,related_field_types=?,status=1,updated_at=NOW() WHERE name=?`,
			strings.Title(strings.ReplaceAll(rel.Name, "_", " ")), strings.Join(ids, ","), fieldTypes, rel.Name)
		if err != nil {
			return err
		}
		_, err = database.ExecContext(ctx, `INSERT INTO ice_relationships (name,label,modules,related_field_types,status,created_at,updated_at)
			SELECT ?,?,?,?,1,NOW(),NOW() WHERE NOT EXISTS (SELECT 1 FROM ice_relationships WHERE name=?)`,
			rel.Name, strings.Title(strings.ReplaceAll(rel.Name, "_", " ")), strings.Join(ids, ","), fieldTypes, rel.Name)
		if err != nil {
			return err
		}
		relID, err := relationshipIDByName(ctx, database, rel.Name)
		if err != nil {
			return err
		}
		for _, raw := range ids {
			_, err = database.ExecContext(ctx, `INSERT INTO ice_relationship_modules (relationship_id,module_id,created_at,updated_at) SELECT ?,?,NOW(),NOW() WHERE NOT EXISTS (SELECT 1 FROM ice_relationship_modules WHERE relationship_id=? AND module_id=?)`,
				relID, raw, relID, raw)
			if err != nil {
				return err
			}
		}
	}
	return nil
}

func seedDefaultSubpanels(ctx context.Context, database *sql.DB) error {
	subpanels := []struct {
		Name         string
		Label        string
		Module       string
		Relationship string
	}{
		{"accounts_contacts", "Contacts", "accounts", "accounts_contacts"},
		{"contacts_accounts", "Accounts", "contacts", "accounts_contacts"},
		{"accounts_opportunities", "Opportunities", "accounts", "accounts_opportunities"},
		{"opportunities_accounts", "Accounts", "opportunities", "accounts_opportunities"},
		{"accounts_cases", "Cases", "accounts", "accounts_cases"},
		{"accounts_contracts", "Contracts", "accounts", "accounts_contracts"},
		{"accounts_meetings", "Meetings", "accounts", "accounts_meetings"},
		{"contracts_lineitems", "Line Items", "contracts", "contracts_lineitems"},
		{"projects_tasks", "Tasks", "projects", "projects_tasks"},
	}
	for _, item := range subpanels {
		moduleID, err := moduleIDByName(ctx, database, item.Module)
		if err != nil || moduleID == 0 {
			continue
		}
		relID, err := relationshipIDByName(ctx, database, item.Relationship)
		if err != nil || relID == 0 {
			continue
		}
		_, err = database.ExecContext(ctx, `UPDATE ice_module_subpanels SET label=?,module_id=?,relationship_id=?,status=1,updated_at=NOW() WHERE name=?`,
			item.Label, moduleID, relID, item.Name)
		if err != nil {
			return err
		}
		res, err := database.ExecContext(ctx, `INSERT INTO ice_module_subpanels (name,label,module_id,relationship_id,status,created_at,updated_at)
			SELECT ?,?,?,?,1,NOW(),NOW() WHERE NOT EXISTS (SELECT 1 FROM ice_module_subpanels WHERE name=?)`,
			item.Name, item.Label, moduleID, relID, item.Name)
		if err != nil {
			return err
		}
		_ = res
	}
	return nil
}

func seedWorkflow(ctx context.Context, database *sql.DB) error {
	steps := []struct {
		Primary string
		Module  string
		Level   int
	}{
		{"leads", "contacts", 1},
		{"contacts", "accounts", 2},
		{"accounts", "quotes", 3},
		{"quotes", "opportunities", 4},
		{"opportunities", "contracts", 5},
	}
	for _, step := range steps {
		primaryID, _ := moduleIDByName(ctx, database, step.Primary)
		moduleID, _ := moduleIDByName(ctx, database, step.Module)
		if primaryID == 0 || moduleID == 0 {
			continue
		}
		_, err := database.ExecContext(ctx, `INSERT INTO ice_module_convertables (primary_module_id,module_id,level,created_at,updated_at) SELECT ?,?,?,NOW(),NOW() WHERE NOT EXISTS (SELECT 1 FROM ice_module_convertables WHERE primary_module_id=? AND module_id=?)`,
			primaryID, moduleID, step.Level, primaryID, moduleID)
		if err != nil {
			return err
		}
	}
	return nil
}

func moduleIDByName(ctx context.Context, database *sql.DB, name string) (int64, error) {
	var id int64
	err := database.QueryRowContext(ctx, `SELECT id FROM ice_modules WHERE name=? LIMIT 1`, name).Scan(&id)
	return id, err
}

func relationshipIDByName(ctx context.Context, database *sql.DB, name string) (int64, error) {
	var id int64
	err := database.QueryRowContext(ctx, `SELECT id FROM ice_relationships WHERE name=? LIMIT 1`, name).Scan(&id)
	return id, err
}

func GenerateModuleTables(ctx context.Context, database *sql.DB) error {
	modules, err := Modules(ctx, database, true)
	if err != nil {
		return err
	}
	for _, raw := range modules {
		name, _ := raw["name"].(string)
		if !ValidIdent(name) {
			continue
		}
		createTable := toInt(raw["create_table"])
		if createTable != 1 {
			continue
		}
		id := toInt64(raw["id"])
		fields, err := Fields(ctx, database, id, "All")
		if err != nil {
			return err
		}
		cols := []string{`id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY`}
		for _, field := range fields {
			if !ValidIdent(field.Name) {
				continue
			}
			cols = append(cols, Q(field.Name)+" "+mysqlType(field))
		}
		cols = append(cols,
			`ice_slug VARCHAR(64) NOT NULL DEFAULT '' UNIQUE`,
			`soft_delete INT NOT NULL DEFAULT 0`,
			`created_at TIMESTAMP NULL`,
			`updated_at TIMESTAMP NULL`,
		)
		if _, err := database.ExecContext(ctx, `CREATE TABLE IF NOT EXISTS `+Q(name)+` (`+strings.Join(cols, ",")+`)`); err != nil {
			return err
		}
		for _, field := range fields {
			if !ValidIdent(field.Name) {
				continue
			}
			if err := addColumnIfMissing(ctx, database, name, field.Name, mysqlType(field)); err != nil {
				return err
			}
		}
		for _, base := range []struct {
			name string
			def  string
		}{
			{"ice_slug", "VARCHAR(64) NOT NULL DEFAULT ''"},
			{"soft_delete", "INT NOT NULL DEFAULT 0"},
			{"created_at", "TIMESTAMP NULL"},
			{"updated_at", "TIMESTAMP NULL"},
		} {
			if err := addColumnIfMissing(ctx, database, name, base.name, base.def); err != nil {
				return err
			}
		}
	}
	return GenerateRelationshipTables(ctx, database)
}

func GenerateRelationshipTables(ctx context.Context, database *sql.DB) error {
	rows, err := database.QueryContext(ctx, `SELECT id,name,modules,COALESCE(related_field_types,''),status FROM ice_relationships WHERE status=1`)
	if err != nil {
		return err
	}
	defer rows.Close()
	for rows.Next() {
		var id int64
		var name, modulesCSV, fieldTypesCSV string
		var status int
		if err := rows.Scan(&id, &name, &modulesCSV, &fieldTypesCSV, &status); err != nil {
			return err
		}
		if !ValidIdent(name) {
			continue
		}
		moduleIDs := strings.Split(modulesCSV, ",")
		fieldTypes := strings.Split(fieldTypesCSV, ",")
		cols := []string{`id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY`}
		for i, rawID := range moduleIDs {
			rawID = strings.TrimSpace(rawID)
			if rawID == "" {
				continue
			}
			moduleID := toInt64(rawID)
			module, err := GetModule(ctx, database, moduleID)
			if err != nil || !ValidIdent(module.Name) {
				continue
			}
			kind := "integer"
			if i < len(fieldTypes) && strings.TrimSpace(fieldTypes[i]) != "" {
				kind = strings.TrimSpace(fieldTypes[i])
			}
			field := Field{Name: module.Name + "_id", DataType: kind, IsNullable: 0}
			cols = append(cols, Q(field.Name)+" "+mysqlType(field))
		}
		cols = append(cols, `status INT NOT NULL DEFAULT 1`, `created_at TIMESTAMP NULL`, `updated_at TIMESTAMP NULL`)
		if _, err := database.ExecContext(ctx, `CREATE TABLE IF NOT EXISTS `+Q(name)+` (`+strings.Join(cols, ",")+`)`); err != nil {
			return err
		}
		for i, rawID := range moduleIDs {
			moduleID := toInt64(strings.TrimSpace(rawID))
			module, err := GetModule(ctx, database, moduleID)
			if err != nil || !ValidIdent(module.Name) {
				continue
			}
			kind := "integer"
			if i < len(fieldTypes) && strings.TrimSpace(fieldTypes[i]) != "" {
				kind = strings.TrimSpace(fieldTypes[i])
			}
			field := Field{Name: module.Name + "_id", DataType: kind, IsNullable: 0}
			if err := addColumnIfMissing(ctx, database, name, field.Name, mysqlType(field)); err != nil {
				return err
			}
		}
		for _, base := range []struct {
			name string
			def  string
		}{
			{"status", "INT NOT NULL DEFAULT 1"},
			{"created_at", "TIMESTAMP NULL"},
			{"updated_at", "TIMESTAMP NULL"},
		} {
			if err := addColumnIfMissing(ctx, database, name, base.name, base.def); err != nil {
				return err
			}
		}
		_ = id
	}
	return rows.Err()
}

func addColumnIfMissing(ctx context.Context, database *sql.DB, table, name, def string) error {
	rows, err := database.QueryContext(ctx, `SHOW COLUMNS FROM `+Q(table)+` LIKE '`+strings.ReplaceAll(name, `'`, `''`)+`'`)
	if err != nil {
		return err
	}
	defer rows.Close()
	if rows.Next() {
		return nil
	}
	_, err = database.ExecContext(ctx, `ALTER TABLE `+Q(table)+` ADD COLUMN `+Q(name)+` `+def)
	return err
}

func mysqlType(field Field) string {
	nullable := " NOT NULL"
	if field.IsNullable == 1 {
		nullable = " NULL"
	}
	switch field.DataType {
	case "integer", "bigInteger":
		return "BIGINT" + nullable + defaultSQL(field)
	case "float", "double":
		return "DOUBLE" + nullable + defaultSQL(field)
	case "decimal":
		places := field.DecimalPlaces
		if places == 0 {
			places = 2
		}
		length := field.FieldLength
		if length == 0 {
			length = 12
		}
		return fmt.Sprintf("DECIMAL(%d,%d)%s%s", length, places, nullable, defaultSQL(field))
	case "text", "mediumText", "longText":
		return "TEXT NULL"
	case "dateTime", "timestamp":
		return "TIMESTAMP NULL"
	default:
		length := field.FieldLength
		if length < 1 {
			length = 245
		}
		return fmt.Sprintf("VARCHAR(%d)%s%s", length, nullable, defaultSQL(field))
	}
}

func defaultSQL(field Field) string {
	if field.DefaultValue == "" || field.IsNullable == 1 {
		return ""
	}
	return " DEFAULT '" + strings.ReplaceAll(field.DefaultValue, "'", "''") + "'"
}

func toInt(v any) int {
	return int(toInt64(v))
}

func toInt64(v any) int64 {
	switch t := v.(type) {
	case int64:
		return t
	case uint64:
		return int64(t)
	case int:
		return int64(t)
	case int32:
		return int64(t)
	case uint:
		return int64(t)
	case uint32:
		return int64(t)
	case int8:
		return int64(t)
	case uint8:
		return int64(t)
	case []byte:
		var n int64
		fmt.Sscan(string(t), &n)
		return n
	case string:
		var n int64
		fmt.Sscan(t, &n)
		return n
	default:
		return 0
	}
}
