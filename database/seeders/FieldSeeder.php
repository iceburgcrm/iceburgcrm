<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\Field;
use App\Models\Module;

class FieldSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() : void
    {
        Field::truncate();


        Module::get()->each(function ($module){
            if(method_exists($this, $module->name)){
                Log::info("Pre" . $module->name . "Generated");
                $this->{$module->name}($module->id);
                Log::info($module->name . "Generated");
            }
            else {
                Log::info($module->name . "Not Generated");
            }

        });

    }

    public function Ice_Modules()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'read_only'     => 1,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'label',
            'label'         => 'Label',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'description',
            'label'         => 'Description',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'status',
            'label'         => 'Status',
            'module_id'     => $moduleId,
            'input_type'    => 'checkbox',
            'data_type'     => 'boolean',
        ], $order++));


        $related_module_id=Module::getId('ice_modules');
        Field::insert(Field::getField([
            'name'          => 'parent_id',
            'label'         => 'Parent Module',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'view_order',
            'label'         => 'View Order',
            'module_id'     => $moduleId,
            'input_type'    => 'text',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'admin',
            'label'         => 'Admin Module',
            'module_id'     => $moduleId,
            'input_type'    => 'checkbox',
            'data_type' => 'Boolean',
        ], $order++));
    }

    public function Ice_Fields()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'read_only'     => 1,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'label',
            'label'         => 'Label',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'input_type',
            'label'         => 'Input Type',
            'module_id'     => $moduleId,
            'field_length'  => 16,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'validation',
            'label'         => 'Validation',
            'module_id'     => $moduleId,
            'field_length'  => 16,
            'input_type'    => 'text',
        ], $order++));

        $related_module_id=Module::getId('ice_modules');
        Field::insert(Field::getField([
            'name'          => 'module_id',
            'label'         => 'Module',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'status',
            'label'         => 'Status',
            'module_id'     => $moduleId,
            'input_type'    => 'checkbox',
            'data_type'     => 'boolean',
        ], $order++));

    }

    public function Ice_Module_Subpanels()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'read_only'     => 1,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'label',
            'label'         => 'Label',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'read_only'     => 1,
            'input_type'    => 'text',
        ], $order++));

        $related_module_id=Module::getId('ice_modules');
        Field::insert(Field::getField([
            'name'          => 'module_id',
            'label'         => 'Module',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'list_size',
            'label'         => 'List Size',
            'module_id'     => $moduleId,
            'input_type'    => 'text',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'list_order_column',
            'label'         => 'List Order Column',
            'module_id'     => $moduleId,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'list_order',
            'label'         => 'List Order',
            'module_id'     => $moduleId,
            'input_type'    => 'text',
        ], $order++));


    }

    public function Ice_Datalets()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'read_only'     => 1,
            'input_type'    => 'text',
        ], $order++));


        Field::insert(Field::getField([
            'name'          => 'active',
            'label'         => 'Active',
            'module_id'     => $moduleId,
            'input_type'    => 'checkbox',
            'data_type' => 'Boolean',
        ], $order++));

    }


    public function Ice_Users()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'profile_pic',
            'label'         => 'Image',
            'module_id'     => $moduleId,
            'input_type'    => 'image',
            'data_type'     => 'MEDIUMTEXT',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'password',
            'label'         => 'Password',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type' => 'password'
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'email',
            'label'         => 'Email',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type' => 'email'
        ], $order++));

        $related_module_id=Module::getId('ice_roles');
        Field::insert(Field::getField([
            'name'          => 'role_id',
            'label'         => 'User Role',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'data_type' => 'Integer',
        ], $order++));



    }

    public function Ice_Roles()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Task_Status()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Task_Types()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Case_Status()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Case_Priorities()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Project_Priorities()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Case_Types()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Project_Status()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Project_Types()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Quote_Status()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Task_Priorities()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Group_Types()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }

    public function Products()
    {
        $order=0;

        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'text',
        ], $order++));
    }


    public function Countries()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name'          => 'code',
            'label'         => 'Code',
            'module_id'     => $moduleId,
            'field_length'  => 16,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'name',
            'module_id'     => $moduleId,
            'field_length'  => 100,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'flag',
            'label'         => 'Flag',
            'module_id'     => $moduleId,
            'input_type'    => 'image',
            'data_type'     => 'MEDIUMTEXT',
        ], $order++));
    }

    public function Currency()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);
        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 32,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'code',
            'label'         => 'Code',
            'module_id'     => $moduleId,
            'field_length'  => 3,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'symbol',
            'label'         => 'Symbol',
            'module_id'     => $moduleId,
            'field_length'  => 3,
        ], $order++));
    }

    public function Contract_Status()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);


        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'name',
            'module_id'     => $moduleId,
            'field_length'  => 32,
        ], $order++));
    }

    public function Account_Status()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);


        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'name',
            'module_id'     => $moduleId,
            'field_length'  => 32,
        ], $order++));
    }

    public function Ice_Themes()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);


        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'name',
            'module_id'     => $moduleId,
            'field_length'  => 32,
        ], $order++));
    }

    public function Accounts()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name'          => 'company_logo',
            'label'         => 'Company Logo',
            'module_id'     => $moduleId,
            'input_type'    => 'image',
            'data_type'     => 'MEDIUMTEXT',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'validation'    => 'required|max:200',
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'first_name',
            'label'         => 'First Name',
            'validation'    => 'required|max:50',
            'required'      => 1,
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'last_name',
            'label'         => 'Last Name',
            'required'      => 1,
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'color',
            'label'         => 'Brand Color',
            'module_id'     => $moduleId,
            'input_type'    => 'color'
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'email',
            'label'         => 'Email',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'email',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'phone',
            'label'         => 'Phone',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type'    => 'tel',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'fax',
            'label'         => 'Fax',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type'    => 'tel',
        ], $order++));



        Field::insert(Field::getField([
            'name'          => 'website',
            'label'         => 'Website',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'url',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'address',
            'label'         => 'Address',
            'module_id'     => $moduleId,
            'field_length'  => 128,
            'input_type'    => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'city',
            'label'         => 'City',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'city',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'zip',
            'label'         => 'Zip',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id=Module::getId('states');
        Field::insert(Field::getField([
            'name'          => 'state',
            'label'         => 'State',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'data_type' => 'Integer',
        ], $order++));

        $related_module_id=Module::getId('countries');
        Field::insert(Field::getField([
            'name'          => 'country',
            'label'         => 'Country',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'description',
            'label'         => 'Description',
            'module_id'     => $moduleId,
            'field_length'  => 128,
            'input_type'    => 'textarea',
        ], $order++));

        $related_module_id=Module::getId('account_status');
        Field::insert(Field::getField([
            'name'          => 'status',
            'label'         => 'Status',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id=Module::getId('ice_users');
        Field::insert(Field::getField([
            'name'          => 'assigned_to',
            'label'         => 'Assigned To',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

    }

    public function Contacts()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name'          => 'profile_pic',
            'label'         => 'Image',
            'module_id'     => $moduleId,
            'input_type'    => 'image',
            'data_type'     => 'MEDIUMTEXT',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'first_name',
            'label'         => 'First Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'last_name',
            'label'         => 'Last Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'email',
            'label'         => 'Email',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'email',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'phone',
            'label'         => 'Phone',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type'    => 'tel',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'fax',
            'label'         => 'Fax',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type'    => 'tel',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'website',
            'label'         => 'Website',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'url',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'address',
            'label'         => 'Address',
            'module_id'     => $moduleId,
            'field_length'  => 128,
            'input_type'    => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'city',
            'label'         => 'City',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'city',
        ], $order++));

        $related_module_id=Module::getId('states');
        Field::insert(Field::getField([
            'name'          => 'state',
            'label'         => 'State',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'zip',
            'label'         => 'Zip',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id=Module::getId('countries');
        Field::insert(Field::getField([
            'name'          => 'country',
            'label'         => 'Country',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'description',
            'label'         => 'Description',
            'module_id'     => $moduleId,
            'field_length'  => 128,
            'input_type'    => 'textarea',
        ], $order++));

        $related_module_id=Module::getId('contract_status');
        Field::insert(Field::getField([
            'name'          => 'status',
            'label'         => 'Status',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'email_receive',
            'label'         => 'Email Opt Out',
            'module_id'     => $moduleId,
            'input_type'    => 'checkbox',
            'data_type' => 'boolean',
        ], $order++));

        $related_module_id=Module::getId('ice_users');
        Field::insert(Field::getField([
            'name'          => 'assigned_to',
            'label'         => 'Assigned To',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

    }

    public function Contracts()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);


        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'description',
            'label'         => 'Description',
            'module_id'     => $moduleId,
            'field_length'  => 128,
            'input_type'    => 'textarea',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'discount',
            'label'         => 'Discount',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'taxes',
            'label'         => 'Taxes',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));


        Field::insert(Field::getField([
            'name'          => 'shipping',
            'label'         => 'Shipping',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'subtotal',
            'label'         => 'Subtotal',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));


        Field::insert(Field::getField([
            'name'          => 'total',
            'label'         => 'Total',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        $related_module_id=Module::getId('currency');
        Field::insert(Field::getField([
            'name'          => 'currency',
            'label'         => 'Currency',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id=Module::getId('contacts');
        Field::insert(Field::getField([
            'name'          => 'signed_by',
            'label'         => 'Signed By',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'last_name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id=Module::getId('ice_users');
        Field::insert(Field::getField([
            'name'          => 'assigned_to',
            'label'         => 'Assigned To',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id=Module::getId('contract_types');
        Field::insert(Field::getField([
            'name'          => 'contract_type',
            'label'         => 'Contract Type',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'start_date',
            'label'         => 'Start Date',
            'module_id'     => $moduleId,
            'input_type'    => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'end_date',
            'label'         => 'End Date',
            'module_id'     => $moduleId,
            'input_type'    => 'date',
            'data_type' => 'integer',
        ], $order++));
    }

    public function Lineitems()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);


        $related_module_id=Module::getId('products');
        Field::insert(Field::getField([
            'name'          => 'product_id',
            'label'         => 'Product',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'quantity',
            'label'         => 'Quantity',
            'module_id'     => $moduleId,
            'input_type'    => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'price',
            'label'         => 'Price',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'unit_price',
            'label'         => 'Unit Price',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'cost',
            'label'         => 'Cost',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'discount',
            'label'         => 'Discount',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        $related_module_id=Module::getId('discount_types');
        Field::insert(Field::getField([
            'name'          => 'discount_type',
            'label'         => 'Discount Type',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'taxes',
            'label'         => 'Taxes',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'gross',
            'label'         => 'Gross',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'net',
            'label'         => 'Net',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));


        Field::insert(Field::getField([
            'name'          => 'description',
            'label'         => 'Description',
            'module_id'     => $moduleId,
            'field_length'  => 128,
            'input_type'    => 'textarea',
        ], $order++));


    }

    public function Opportunities()
    {
        $order=0;
        $moduleId=Module::getId(__FUNCTION__);


        Field::insert(Field::getField([
            'name'          => 'name',
            'label'         => 'Name',
            'module_id'     => $moduleId,
            'field_length'  => 128,
        ], $order++));

        $related_module_id=Module::getId('ice_users');
        Field::insert(Field::getField([
            'name'          => 'assigned_to',
            'label'         => 'Assigned To',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id=Module::getId('opportunity_types');
        Field::insert(Field::getField([
            'name'          => 'type',
            'label'         => 'Type',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'amount',
            'label'         => 'Amount',
            'module_id'     => $moduleId,
            'input_type'    => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'probability',
            'label'         => 'Probability',
            'module_id'     => $moduleId,
            'input_type'    => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'close_date',
            'label'         => 'Close Date',
            'module_id'     => $moduleId,
            'input_type'    => 'date',
            'data_type' => 'integer',
        ], $order++));

        $related_module_id=Module::getId('opportunity_status');
        Field::insert(Field::getField([
            'name'          => 'status',
            'label'         => 'Status',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));
/*
        $related_module_id=Module::getId('sales_stage');
        Field::insert(Field::getField([
            'name'          => 'sales_stage',
            'label'         => 'Sales Stage',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));
  */

    }

    public function Orders()
    {
        $moduleId = Module::getId(__FUNCTION__);
        $order=0;

        Field::insert(Field::getField([
            'name'          => 'first_name',
            'label'         => 'First Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'last_name',
            'label'         => 'Last Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'email',
            'label'         => 'Email',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'email',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'phone',
            'label'         => 'Phone',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type'    => 'tel',
        ], $order++));

        $related_module_id = Module::getId('quote_status');
        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('currency');
        Field::insert(Field::getField([
            'name' => 'currency',
            'label' => 'Currency',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'amount',
            'label' => 'Amount',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'tax',
            'label' => 'Tax',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'total',
            'label' => 'Total',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'subtotal',
            'label' => 'Subtotal',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'discount',
            'label' => 'Discount',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_address',
            'label' => 'Billing Address',
            'module_id' => $moduleId,
            'field_length' => 128,
            'input_type' => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_city',
            'label' => 'Billing City',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'city',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_zip',
            'label' => 'Billing Zip',
            'module_id' => $moduleId,
            'field_length' => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id = Module::getId('states');
        Field::insert(Field::getField([
            'name' => 'billing_state',
            'label' => 'Billing State',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        $related_module_id = Module::getId('countries');
        Field::insert(Field::getField([
            'name' => 'billing_country',
            'label' => 'Billing Country',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_address',
            'label' => 'Shipping Address',
            'module_id' => $moduleId,
            'field_length' => 128,
            'input_type' => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_city',
            'label' => 'Shipping City',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'city',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_zip',
            'label' => 'Shipping Zip',
            'module_id' => $moduleId,
            'field_length' => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id = Module::getId('states');
        Field::insert(Field::getField([
            'name' => 'shipping_state',
            'label' => 'Shipping State',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        $related_module_id = Module::getId('countries');
        Field::insert(Field::getField([
            'name' => 'shipping_country',
            'label' => 'Shipping Country',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));


        $related_module_id=Module::getId('products');
        Field::insert(Field::getField([
            'name'          => 'product',
            'label'         => 'Product',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

    }


    public function Sales()
    {
        $moduleId = Module::getId(__FUNCTION__);
        $order=0;

        Field::insert(Field::getField([
            'name'          => 'total',
            'label'         => 'Total',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'data_type' => 'Integer',
        ], $order++));
    }

    public function Leads()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name'          => 'first_name',
            'label'         => 'First Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'last_name',
            'label'         => 'Last Name',
            'module_id'     => $moduleId,
            'field_length'  => 64,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'email',
            'label'         => 'Email',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'email',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'phone',
            'label'         => 'Phone',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type'    => 'tel',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'fax',
            'label'         => 'Fax',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type'    => 'tel',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'website',
            'label'         => 'Website',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'url',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'address',
            'label'         => 'Address',
            'module_id'     => $moduleId,
            'field_length'  => 128,
            'input_type'    => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'city',
            'label'         => 'City',
            'module_id'     => $moduleId,
            'field_length'  => 64,
            'input_type'    => 'city',
        ], $order++));

        $related_module_id=Module::getId('states');
        Field::insert(Field::getField([
            'name'          => 'state',
            'label'         => 'State',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'data_type' => 'Integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'zip',
            'label'         => 'Zip',
            'module_id'     => $moduleId,
            'field_length'  => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id=Module::getId('countries');
        Field::insert(Field::getField([
            'name'          => 'country',
            'label'         => 'Country',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'description',
            'label'         => 'Description',
            'module_id'     => $moduleId,
            'field_length'  => 128,
            'input_type'    => 'textarea',
        ], $order++));

        $related_module_id=Module::getId('lead_status');
        Field::insert(Field::getField([
            'name'          => 'status',
            'label'         => 'Status',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'email_receive',
            'label'         => 'Email Opt Out',
            'module_id'     => $moduleId,
            'input_type'    => 'checkbox',
            'data_type' => 'boolean',
        ], $order++));

        $related_module_id=Module::getId('ice_users');
        Field::insert(Field::getField([
            'name'          => 'assigned_to',
            'label'         => 'Assigned To',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id=Module::getId('lead_types');
        Field::insert(Field::getField([
            'name'          => 'lead_type',
            'label'         => 'Lead Type',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id=Module::getId('lead_sources');
        Field::insert(Field::getField([
            'name'          => 'lead_source',
            'label'         => 'Lead Source',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));


    }

    public function Meetings()
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
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 128,
            'input_type' => 'textarea',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'start_date',
            'label' => 'Start Date',
            'module_id' => $moduleId,
            'data_type' => 'integer',
            'input_type' => 'date',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'end_date',
            'label' => 'End Date',
            'module_id' => $moduleId,
            'data_type' => 'integer',
            'input_type' => 'date',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'start_time',
            'label' => 'Start Time',
            'module_id' => $moduleId,
            'data_type' => 'integer',
            'input_type' => 'date',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'end_time',
            'label' => 'End Time',
            'module_id' => $moduleId,
            'data_type' => 'integer',
            'input_type' => 'date',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'reminder_time',
            'label' => 'Reminder Time',
            'module_id' => $moduleId,
            'data_type' => 'integer',
            'input_type' => 'date',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'location',
            'label' => 'Location',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'city',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'phone',
            'label' => 'Phone',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'tel',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'link',
            'label' => 'Link',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'url',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'meeting_password',
            'label' => 'Meeting Password',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'password',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'video_recording',
            'label'         => 'Video Recording',
            'module_id'     => $moduleId,
            'input_type'    => 'video',
            'data_type'     => 'MEDIUMTEXT',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'audio_recording',
            'label'         => 'Audio Recording',
            'module_id'     => $moduleId,
            'input_type'    => 'audio',
            'data_type'     => 'MEDIUMTEXT',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'contract',
            'label' => 'Contact',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'last_name',
            'related_module_id' => Module::getId('contacts'),
        ], $order++));


        Field::insert(Field::getField([
            'name' => 'types',
            'label' => 'Types',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => Module::getId('meeting_types'),
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => Module::getId('meeting_status'),
        ], $order++));

        $related_module_id=Module::getId('ice_users');
        Field::insert(Field::getField([
            'name'          => 'assigned_to',
            'label'         => 'Assigned To',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

    }
    public function Notes()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'subject',
            'label' => 'Subject',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 190,
            'input_type' => 'textarea',
        ], $order++));

        $related_module_id=Module::getId('ice_users');
        Field::insert(Field::getField([
            'name'          => 'assigned_to',
            'label'         => 'Assigned To',
            'module_id'     => $moduleId,
            'input_type'    => 'related',
            'data_type' => 'integer',
            'related_field_id'=> 'id',
            'related_value_id'=> 'name',
            'related_module_id' => $related_module_id,
        ], $order++));


    }



    public function Tasks()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'subject',
            'label' => 'Subject',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 190,
            'input_type' => 'textarea',
        ], $order++));

        $related_module_id = Module::getId('ice_users');
        Field::insert(Field::getField([
            'name' => 'assigned_to',
            'label' => 'Assigned To',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('task_types');
        Field::insert(Field::getField([
            'name' => 'task_types',
            'label' => 'Task Type',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('task_status');
        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('task_priorities');
        Field::insert(Field::getField([
            'name' => 'task_priority',
            'label' => 'Task Priority',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'due_date',
            'label' => 'Due Date',
            'module_id' => $moduleId,
            'input_type' => 'date',
            'data_type' => 'integer',
        ], $order++));
    }

    public function Campaigns(){

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
                'name' => 'description',
                'label' => 'Description',
                'module_id' => $moduleId,
                'field_length' => 190,
                'input_type' => 'textarea',
            ], $order++));

            $related_module_id = Module::getId('ice_users');
            Field::insert(Field::getField([
                'name' => 'assigned_to',
                'label' => 'Assigned To',
                'module_id' => $moduleId,
                'input_type' => 'related',
                'data_type' => 'integer',
                'related_field_id' => 'id',
                'related_value_id' => 'name',
                'related_module_id' => $related_module_id,
            ], $order++));

        $related_module_id = Module::getId('campaign_status');
        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'budget',
            'label' => 'Budget',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'forecast',
            'label' => 'Forecast',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'actual',
            'label' => 'Actual',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length'  => 8,
            'decimal_places' => 2,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'impressions',
            'label' => 'Impressions',
            'module_id' => $moduleId,
            'input_type' => 'number',
            'data_type' => 'integer',
        ], $order++));

        $related_module_id = Module::getId('currency');
        Field::insert(Field::getField([
            'name' => 'currency',
            'label' => 'Currency',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('campaign_types');
        Field::insert(Field::getField([
            'name' => 'campaign_type',
            'label' => 'Campaign Type',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'creative',
            'label'         => 'Creative',
            'module_id'     => $moduleId,
            'input_type'    => 'video',
            'data_type'     => 'MEDIUMTEXT',
        ], $order++));


    }

    public function Cases()
    {

        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'subject',
            'label' => 'Subject',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 190,
            'input_type' => 'textarea',
        ], $order++));

        $related_module_id = Module::getId('ice_users');
        Field::insert(Field::getField([
            'name' => 'assigned_to',
            'label' => 'Assigned To',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'case_number',
            'label' => 'Case Number',
            'module_id' => $moduleId,
            'input_type' => 'number',
            'data_type' => 'integer',
        ], $order++));

        $related_module_id = Module::getId('case_status');
        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('case_priorities');
        Field::insert(Field::getField([
            'name' => 'priority',
            'label' => 'Priority',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('case_types');
        Field::insert(Field::getField([
            'name' => 'type',
            'label' => 'Type',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));


        Field::insert(Field::getField([
            'name' => 'resolution',
            'label' => 'Resolution',
            'module_id' => $moduleId,
            'field_length' => 190,
            'input_type' => 'textarea',
        ], $order++));


    }

    public function Projects()
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
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 190,
            'input_type' => 'textarea',
        ], $order++));

        $related_module_id = Module::getId('ice_users');
        Field::insert(Field::getField([
            'name' => 'assigned_to',
            'label' => 'Assigned To',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('project_status');
        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('project_priorities');
        Field::insert(Field::getField([
            'name' => 'priority',
            'label' => 'Priority',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('project_types');
        Field::insert(Field::getField([
            'name' => 'type',
            'label' => 'Type',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'start_date',
            'label'         => 'Start Date',
            'module_id'     => $moduleId,
            'input_type'    => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'end_date',
            'label'         => 'End Date',
            'module_id'     => $moduleId,
            'input_type'    => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'due_date',
            'label'         => 'Due Date',
            'module_id'     => $moduleId,
            'input_type'    => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'completed_date',
            'label'         => 'Completed Date',
            'module_id'     => $moduleId,
            'input_type'    => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'estimated_hours',
            'label'         => 'Estimated Hours',
            'module_id'     => $moduleId,
            'input_type'    => 'number',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name'          => 'actual_hours',
            'label'         => 'Actual Hours',
            'module_id'     => $moduleId,
            'input_type'    => 'number',
            'data_type' => 'integer',
        ], $order++));

    }

    public function Quotes()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);


        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

        $related_module_id = Module::getId('ice_users');
        Field::insert(Field::getField([
            'name' => 'assigned_to',
            'label' => 'Assigned To',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('quote_status');
        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('currency');
        Field::insert(Field::getField([
            'name' => 'currency',
            'label' => 'Currency',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'amount',
            'label' => 'Amount',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'tax',
            'label' => 'Tax',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'total',
            'label' => 'Total',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'subtotal',
            'label' => 'Subtotal',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'discount',
            'label' => 'Discount',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_address',
            'label' => 'Billing Address',
            'module_id' => $moduleId,
            'field_length' => 128,
            'input_type' => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_city',
            'label' => 'Billing City',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'city',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_zip',
            'label' => 'Billing Zip',
            'module_id' => $moduleId,
            'field_length' => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id = Module::getId('states');
        Field::insert(Field::getField([
            'name' => 'billing_state',
            'label' => 'Billing State',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        $related_module_id = Module::getId('countries');
        Field::insert(Field::getField([
            'name' => 'billing_country',
            'label' => 'Billing Country',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_address',
            'label' => 'Shipping Address',
            'module_id' => $moduleId,
            'field_length' => 128,
            'input_type' => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_city',
            'label' => 'Shipping City',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'city',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_zip',
            'label' => 'Shipping Zip',
            'module_id' => $moduleId,
            'field_length' => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id = Module::getId('states');
        Field::insert(Field::getField([
            'name' => 'shipping_state',
            'label' => 'Shipping State',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        $related_module_id = Module::getId('countries');
        Field::insert(Field::getField([
            'name' => 'shipping_country',
            'label' => 'Shipping Country',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'expire_date',
            'label' => 'Expire Date',
            'module_id' => $moduleId,
            'input_type' => 'date',
            'data_type' => 'integer',
        ], $order++));
    }

    public function Invoices()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);


        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

        $related_module_id = Module::getId('ice_users');
        Field::insert(Field::getField([
            'name' => 'assigned_to',
            'label' => 'Assigned To',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('invoice_status');
        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('currency');
        Field::insert(Field::getField([
            'name' => 'currency',
            'label' => 'Currency',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'amount',
            'label' => 'Amount',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'tax',
            'label' => 'Tax',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'total',
            'label' => 'Total',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'subtotal',
            'label' => 'Subtotal',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'discount',
            'label' => 'Discount',
            'module_id' => $moduleId,
            'input_type' => 'currency',
            'data_type' => 'float',
            'field_length' => '8',
            'decimal_places' => '2',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_address',
            'label' => 'Billing Address',
            'module_id' => $moduleId,
            'field_length' => 128,
            'input_type' => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_city',
            'label' => 'Billing City',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'city',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'billing_zip',
            'label' => 'Billing Zip',
            'module_id' => $moduleId,
            'field_length' => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id = Module::getId('states');
        Field::insert(Field::getField([
            'name' => 'billing_state',
            'label' => 'Billing State',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        $related_module_id = Module::getId('countries');
        Field::insert(Field::getField([
            'name' => 'billing_country',
            'label' => 'Billing Country',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_address',
            'label' => 'Shipping Address',
            'module_id' => $moduleId,
            'field_length' => 128,
            'input_type' => 'address',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_city',
            'label' => 'Shipping City',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'city',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'shipping_zip',
            'label' => 'Shipping Zip',
            'module_id' => $moduleId,
            'field_length' => 32,
            'input_type' => 'zip'
        ], $order++));

        $related_module_id = Module::getId('states');
        Field::insert(Field::getField([
            'name' => 'shipping_state',
            'label' => 'Shipping State',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'related_module_id' => $related_module_id,
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'data_type' => 'Integer',
        ], $order++));

        $related_module_id = Module::getId('countries');
        Field::insert(Field::getField([
            'name' => 'shipping_country',
            'label' => 'Shipping Country',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'sign_date',
            'label' => 'Sign Date',
            'module_id' => $moduleId,
            'input_type' => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'expire_date',
            'label' => 'Expire Date',
            'module_id' => $moduleId,
            'input_type' => 'date',
            'data_type' => 'integer',
        ], $order++));


    }


    public function States()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'code',
            'label' => 'Code',
            'module_id' => $moduleId,
            'field_length' => 4,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'abbreviation',
            'label' => 'Abbreviation',
            'module_id' => $moduleId,
            'field_length' => 4,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 120,
        ], $order++));
    }

    public function Document_Types()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }


    public function Document_Status()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Documents()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 128,
            'input_type' => 'textarea',
        ], $order++));

        $related_module_id = Module::getId('ice_users');
        Field::insert(Field::getField([
            'name' => 'assigned_to',
            'label' => 'Assigned To',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'file_link',
            'label' => 'Link Url',
            'module_id' => $moduleId,
            'field_length' => 64,
        ], $order++));

        $related_module_id = Module::getId('document_types');
        Field::insert(Field::getField([
            'name' => 'document_type',
            'label' => 'Document Type',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('document_status');
        Field::insert(Field::getField([
            'name' => 'document_status',
            'label' => 'Document Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'expire_date',
            'label' => 'Expire Date',
            'module_id' => $moduleId,
            'input_type' => 'date',
            'data_type' => 'integer',
        ], $order++));

    }

    public function Activities()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

        $related_module_id = Module::getId('ice_users');
        Field::insert(Field::getField([
            'name' => 'assigned_to',
            'label' => 'Assigned To',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'date_due',
            'label' => 'Date Due',
            'module_id' => $moduleId,
            'input_type' => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'date_start',
            'label' => 'Date Start',
            'module_id' => $moduleId,
            'input_type' => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'date_finish',
            'label' => 'Date Finish',
            'module_id' => $moduleId,
            'input_type' => 'date',
            'data_type' => 'integer',
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'duration',
            'label' => 'Duration',
            'module_id' => $moduleId,
            'input_type' => 'integer',
            'data_type' => 'integer',
        ], $order++));

        $related_module_id = Module::getId('activity_priorities');
        Field::insert(Field::getField([
            'name' => 'priority',
            'label' => 'Priority',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('activity_status');
        Field::insert(Field::getField([
            'name' => 'status',
            'label' => 'Status',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

        $related_module_id = Module::getId('activity_type');
        Field::insert(Field::getField([
            'name' => 'type',
            'label' => 'Type',
            'module_id' => $moduleId,
            'input_type' => 'related',
            'data_type' => 'integer',
            'related_field_id' => 'id',
            'related_value_id' => 'name',
            'related_module_id' => $related_module_id,
        ], $order++));

    }

    public function Groups()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

        Field::insert(Field::getField([
            'name' => 'description',
            'label' => 'Description',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));

    }

    public function Contract_Types()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Discount_Types()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Opportunity_Types()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }


    public function Opportunity_Status()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Lead_Types()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Lead_Sources()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Lead_Status()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Meeting_Status()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Meeting_Type()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Campaign_Status()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Campaign_Types()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Meeting_Types()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }

    public function Invoice_Status()
    {
        $order = 0;
        $moduleId = Module::getId(__FUNCTION__);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 128,
        ], $order++));
    }



}
