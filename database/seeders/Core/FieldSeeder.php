<?php

namespace Database\Seeders\Core;

use App\Models\Field;
use App\Models\Module;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Field::truncate();

        Module::get()->each(function ($module) {
            if (method_exists($this, $module->name)) {
                Log::info('Pre'.$module->name.'Generated');
                $this->{$module->name}($module->id);
                Log::info($module->name.'Generated');
            } else {
                Log::info($module->name.'Not Generated');
            }

        });

    }

    public function Ice_Modules()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 64,
            'read_only' => 1,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'label',
            'label' => 'Label',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'checkbox',
            'data_type' => 'boolean',
        ], $order++));

        $related_module_id = Module::getId('ice_modules');
        Field::insert(Field::getField([
            'name' => 'parent_id',
            'label' => 'Parent Module',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'view_order',
            'label' => 'View Order',
            'module_id' => $moduleId,
            'input_type' => 'text',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'admin',
            'label' => 'Admin Module',
            'module_id' => $moduleId,
            'input_type' => 'checkbox',
            'data_type' => 'Boolean',
        ], $order++));
    }

    public function Ice_Fields()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 64,
            'read_only' => 1,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'label',
            'label' => 'Label',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'input_type',
            'label' => 'Input Type',
            'module_id' => $moduleId,
            'field_length' => 16,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'validation',
            'label' => 'Validation',
            'module_id' => $moduleId,
            'field_length' => 16,
            'input_type' => 'text',
        ], $order++));

        $related_module_id = Module::getId('ice_modules');
        Field::insert(Field::getField([
            'name' => 'module_id',
            'label' => 'Module',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'checkbox',
            'data_type' => 'boolean',
        ], $order++));

    }

    public function Ice_Module_Subpanels()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 64,
            'read_only' => 1,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'label',
            'label' => 'Label',
            'module_id' => $moduleId,
            'field_length' => 64,
            'read_only' => 1,
            'input_type' => 'text',
        ], $order++));

        $related_module_id = Module::getId('ice_modules');
        Field::insert(Field::getField([
            'name' => 'module_id',
            'label' => 'Module',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'list_size',
            'label' => 'List Size',
            'module_id' => $moduleId,
            'input_type' => 'text',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'list_order_column',
            'label' => 'List Order Column',
            'module_id' => $moduleId,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'list_order',
            'label' => 'List Order',
            'module_id' => $moduleId,
            'input_type' => 'text',
        ], $order++));

    }

    public function Ice_Datalets()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 64,
            'read_only' => 1,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'label',
            'label' => 'Label',
            'module_id' => $moduleId,
            'field_length' => 64,
            'read_only' => 1,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'type',
            'label' => 'Type',
            'module_id' => $moduleId,
            'input_type' => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'field_id',
            'label' => 'Field ID',
            'module_id' => $moduleId,
            'input_type' => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'module_id',
            'label' => 'Module ID',
            'module_id' => $moduleId,
            'input_type' => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'relationship_id',
            'label' => 'Relationship ID',
            'module_id' => $moduleId,
            'input_type' => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'display_order',
            'label' => 'Display Order',
            'module_id' => $moduleId,
            'input_type' => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'type',
            'label' => 'Type',
            'module_id' => $moduleId,
            'input_type' => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'active',
            'label' => 'Active',
            'module_id' => $moduleId,
            'input_type' => 'checkbox',
            'data_type' => 'Boolean',
        ], $order++));

    }

    public function Ice_Users()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'profile_pic',
            'label' => 'Image',
            'module_id' => $moduleId,
            'input_type' => 'image',
            'data_type' => 'MEDIUMTEXT',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'password',
            'label' => 'Password',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'password',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'email',
            'label' => 'Email',
            'module_id' => $moduleId,
            'field_length' => 32,
            'input_type' => 'email',
        ], $order++));

        $related_module_id = Module::getId('ice_roles');
        Field::insert(Field::getField([
            'name' => 'role_id',
            'label' => 'User Role',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

    }

    public function Ice_Roles()
    {
        $order = 0;

        $moduleId = Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));
    }

    public function Ice_Themes()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'name',
            'module_id' => $moduleId,
            'field_length' => 32,
        ], $order++));
    }

    public function Ice_Relationships()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'modules',
            'label' => 'Module List',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'related_field_types',
            'label' => 'Related Field Types',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'checkbox',
            'data_type' => 'boolean',
        ], $order++));
    }


}
