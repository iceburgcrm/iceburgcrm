<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ice_modules', function (Blueprint $table) {
            $table->index('name', 'ice_modules_name_index');
            $table->index(['status', 'view_order'], 'ice_modules_status_view_order_index');
            $table->index('module_group_id', 'ice_modules_module_group_id_index');
        });

        Schema::table('ice_fields', function (Blueprint $table) {
            $table->index(['module_id', 'name'], 'ice_fields_module_id_name_index');
            $table->index(['module_id', 'status'], 'ice_fields_module_id_status_index');
            $table->index(['module_id', 'search_display', 'search_order'], 'ice_fields_module_search_order_index');
            $table->index(['module_id', 'list_display', 'display_order'], 'ice_fields_module_list_order_index');
            $table->index(['module_id', 'edit_display', 'edit_order'], 'ice_fields_module_edit_order_index');
            $table->index('related_module_id', 'ice_fields_related_module_id_index');
        });

        Schema::table('ice_relationships', function (Blueprint $table) {
            $table->index('name', 'ice_relationships_name_index');
            $table->index('status', 'ice_relationships_status_index');
        });

        Schema::table('ice_relationship_modules', function (Blueprint $table) {
            $table->index(['relationship_id', 'module_id'], 'ice_relationship_modules_relationship_module_index');
            $table->index(['module_id', 'relationship_id'], 'ice_relationship_modules_module_relationship_index');
        });

        Schema::table('ice_module_subpanels', function (Blueprint $table) {
            $table->index(['module_id', 'status'], 'ice_module_subpanels_module_status_index');
            $table->index('relationship_id', 'ice_module_subpanels_relationship_id_index');
        });

        Schema::table('ice_subpanel_fields', function (Blueprint $table) {
            $table->index('subpanel_id', 'ice_subpanel_fields_subpanel_id_index');
            $table->index('field_id', 'ice_subpanel_fields_field_id_index');
        });

        Schema::table('ice_settings', function (Blueprint $table) {
            $table->index('name', 'ice_settings_name_index');
        });

        Schema::table('ice_permissions', function (Blueprint $table) {
            $table->index(['role_id', 'module_id', 'can_read'], 'ice_permissions_role_module_read_index');
            $table->index(['role_id', 'module_id', 'can_write'], 'ice_permissions_role_module_write_index');
            $table->index(['role_id', 'module_id', 'can_import'], 'ice_permissions_role_module_import_index');
            $table->index(['role_id', 'module_id', 'can_export'], 'ice_permissions_role_module_export_index');
        });

        Schema::table('ice_logs', function (Blueprint $table) {
            $table->index(['module_id', 'created_at'], 'ice_logs_module_created_at_index');
            $table->index(['user_id', 'created_at'], 'ice_logs_user_created_at_index');
        });

        Schema::table('ice_connectors', function (Blueprint $table) {
            $table->index(['type', 'status'], 'ice_connectors_type_status_index');
        });

        Schema::table('ice_endpoints', function (Blueprint $table) {
            $table->index(['connector_id', 'status'], 'ice_endpoints_connector_status_index');
        });
    }

    public function down(): void
    {
        Schema::table('ice_endpoints', function (Blueprint $table) {
            $table->dropIndex('ice_endpoints_connector_status_index');
        });

        Schema::table('ice_connectors', function (Blueprint $table) {
            $table->dropIndex('ice_connectors_type_status_index');
        });

        Schema::table('ice_logs', function (Blueprint $table) {
            $table->dropIndex('ice_logs_module_created_at_index');
            $table->dropIndex('ice_logs_user_created_at_index');
        });

        Schema::table('ice_permissions', function (Blueprint $table) {
            $table->dropIndex('ice_permissions_role_module_read_index');
            $table->dropIndex('ice_permissions_role_module_write_index');
            $table->dropIndex('ice_permissions_role_module_import_index');
            $table->dropIndex('ice_permissions_role_module_export_index');
        });

        Schema::table('ice_settings', function (Blueprint $table) {
            $table->dropIndex('ice_settings_name_index');
        });

        Schema::table('ice_subpanel_fields', function (Blueprint $table) {
            $table->dropIndex('ice_subpanel_fields_subpanel_id_index');
            $table->dropIndex('ice_subpanel_fields_field_id_index');
        });

        Schema::table('ice_module_subpanels', function (Blueprint $table) {
            $table->dropIndex('ice_module_subpanels_module_status_index');
            $table->dropIndex('ice_module_subpanels_relationship_id_index');
        });

        Schema::table('ice_relationship_modules', function (Blueprint $table) {
            $table->dropIndex('ice_relationship_modules_relationship_module_index');
            $table->dropIndex('ice_relationship_modules_module_relationship_index');
        });

        Schema::table('ice_relationships', function (Blueprint $table) {
            $table->dropIndex('ice_relationships_name_index');
            $table->dropIndex('ice_relationships_status_index');
        });

        Schema::table('ice_fields', function (Blueprint $table) {
            $table->dropIndex('ice_fields_module_id_name_index');
            $table->dropIndex('ice_fields_module_id_status_index');
            $table->dropIndex('ice_fields_module_search_order_index');
            $table->dropIndex('ice_fields_module_list_order_index');
            $table->dropIndex('ice_fields_module_edit_order_index');
            $table->dropIndex('ice_fields_related_module_id_index');
        });

        Schema::table('ice_modules', function (Blueprint $table) {
            $table->dropIndex('ice_modules_name_index');
            $table->dropIndex('ice_modules_status_view_order_index');
            $table->dropIndex('ice_modules_module_group_id_index');
        });
    }
};
