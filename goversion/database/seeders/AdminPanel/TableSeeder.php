<?php
namespace Database\Seeders\AdminPanel;

use App\Models\Field;
use App\Models\Datalet;
use App\Models\DataletType;
use App\Models\Permission;
use App\Models\User;
use App\Models\Module;
use App\Models\ModuleGroup;
use App\Models\ModuleSubpanel;
use App\Models\Relationship;
use App\Models\SubpanelField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class TableSeeder extends Seeder
{
    public $excludeFields = [
        'id', 'ice_slug', 'soft_delete', 'created_at', 'updated_at',
    ];

    public $prefix = '';

    public $icons = [
        'BuildingOffice2Icon',
        'BuildingOffice2Icon',
        'BuildingOfficeIcon',
        'BuildingLibraryIcon',
        'BuildingStorefrontIcon',
        'BriefcaseIcon',
        'HomeIcon',
        'HomeModernIcon',
        'UserPlusIcon',
        'UserMinusIcon',
        'UserCircleIcon',
        'UserIcon',
        'ChatBubbleLeftIcon',
        'CalculatorIcon',
        'CircleStackIcon',
        'BookOpenIcon',
        'Bars4Icon',
        'UsersIcon',
        'LightBulbIcon',
        'MegaphoneIcon',
        'InboxStackIcon',
        'CurrencyDollarIcon',
        'ArrowRightOnRectangleIcon',
        'QueueListIcon',
        'PencilSquareIcon',
        'DocumentIcon',
        'PencilIcon',
        'UserGroupIcon',
        'GlobeAmericasIcon',
        'RectangleGroupIcon',
        'GlobeAltIcon',
        'CurrencyPoundIcon',
        'SparklesIcon',
        'PhoneIcon',
        'Cog6ToothIcon',
    ];

    public function run()
    {
        $data=$this->getDatabaseSchema();
        $this->processSQL($data);

    }

    public function processSQL($parserTables)
    {
        $cnt = 0;
        $module_group_id = 0;
        foreach ($parserTables as $key => $table) {
            $table['label'] = $table['name'];
            $table['name'] = $this->prefix.$table['name'];
            if ($cnt == 0 || ($cnt <= 12 && $cnt % 3 == 1)) {
                $module_group_id = $this->createModuleGroup($table['name'], $table['label']);
            } elseif ($cnt > 12) {
                $module_group_id = 6;
            }

            $module_id = $this->createModule($table['name'], $table['label'], $cnt, $module_group_id, $table['primary_field']);
            $this->createFields($module_id, $table['fields'],$table['name']);
            $cnt++;
        }

        foreach ($parserTables as $key => $table) {
            $table['name'] = $this->prefix.$table['name'];
            $module = Module::where('name', $table['name'])->first();
            if (isset($table['indexes'])) {
                [$relationship_id, $primary_name, $secondary_name] = $this->createRelationship($module->id, $module->name, $table['indexes']);
                $fields = Field::where('module_id', Module::where('name', $secondary_name)->value('id'))->take(3)->get();
                if (intval($relationship_id) > 0) {
                    $this->createModuleSubpanels($module->id, $relationship_id, $primary_name, $secondary_name, $fields);
                }

            }
        }
    }


    public function getDatabaseSchema($data = null)
    {

        $returnData = [];
        $connectionName = DB::getDefaultConnection();
        $databaseName = config("database.connections.{$connectionName}.database");
        $data = DB::select("SELECT * FROM information_schema.columns WHERE table_schema = ? ORDER BY table_name, ordinal_position", [$databaseName]);

        $database = [];
        foreach ($data as $row) {
            //print $row->TABLE_NAME;
            if(str_starts_with($row->TABLE_NAME, "ice_")
                || $row->TABLE_NAME == 'migrations'
                || $row->TABLE_NAME == 'personal_access_tokens'
                || $row->TABLE_NAME == 'input_masks'
                || $row->TABLE_NAME == 'input_types'
            )
            {
                continue;
            }
            $database[$row->TABLE_NAME]['name'] = $row->TABLE_NAME;
            $item = [
                'name' => $row->COLUMN_NAME,
                'type' => $row->DATA_TYPE,
            ];
            if ($row->COLUMN_KEY == 'PRI') {
                $database[$row->TABLE_NAME]['primary_field'] = $row->COLUMN_NAME;
            }
            if (! strpos($row->DATA_TYPE, 'text')) {
                $item['length'] = $row->NUMERIC_PRECISION > 0 ? $row->NUMERIC_PRECISION : $row->CHARACTER_MAXIMUM_LENGTH;
            }
            if ($row->DATA_TYPE == 'decimal') {
                $values = explode(',', str_replace(['decimal('], ')', strtolower($row->COLUMN_TYPE)));
                if (isset($values[1])) {
                    $item['decimals'] = $values[1];
                }
            }
            $database[$row->TABLE_NAME]['fields'][] = $item;
        }
        return $database;
    }

    public function getIcon($id)
    {
        $icon = 'BuildingOffice2Icon';

        if (isset($this->icons[$id])) {
            $icon = $this->icons[$id];
        }

        return $icon;
    }

    public function getInputType($field)
    {
        $inputType = 'text';
        $dataType = 'string';

        if (str_contains($field['name'], 'video')) {
            $inputType = 'video';
        } elseif (str_contains($field['name'], 'image')) {
            $inputType = 'image';
        } elseif (str_contains($field['name'], 'address')) {
            $inputType = 'address';
        } elseif (str_contains($field['name'], 'zip')) {
            $inputType = 'zip';
        } elseif (str_contains($field['name'], 'email')) {
            $inputType = 'email';
        } elseif (str_contains($field['name'], 'phone')) {
            $inputType = 'tel';
        } elseif (str_contains($field['name'], '%website%') || str_contains($field['name'], '%url%')) {
            $inputType = 'url';
        } elseif (str_contains($field['name'], 'number')) {
            $inputType = 'number';
        } elseif (str_contains($field['name'], 'password')) {
            $inputType = 'password';
        }

        $field['type'] = strtoupper($field['type']);

        switch ($field['type']) {
            case 'LONGTEXT':
            case 'MEDIUMTEXT':
            case 'TEXT':
                $dataType = $field['type'];
                break;
            case 'TINYINT':
                $inputType = 'checkbox';
                $dataType = 'integer';
                break;
            case 'SMALLINT':
            case 'BIGINT':
            case 'INT':
                $inputType = 'integer';
                $dataType = 'integer';
                break;
            case 'DATETIME':
                $inputType = 'datetime';
                $dataType = 'datetime';
                break;
            case 'TIMESTAMP':
                $inputType = 'timestamp';
                $dataType = 'timestamp';
                break;
            case 'DATE':
                $inputType = 'date';
                $dataType = 'integer';
                break;
            case 'REAL':
            case 'DOUBLE':
            case 'DOUBLE PRECISION':
            case 'FLOAT':
            case 'DECIMAL':
                $inputType = 'currency';
                $dataType = 'float';
                break;
            default:
                break;
        }

        return [$inputType, $dataType];
    }

    public function createFields($moduleId, $fields, $tableName='')
    {
        $cnt = 0;
        if(!isset($fields['name']['created_at'])){
            Schema::table($tableName, function ($table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
            });
        }
        if(!isset($fields['name']['updated_at'])){
            Schema::table($tableName, function ($table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }
        if(!isset($fields['name']['ice_slug'])){
            Schema::table($tableName, function ($table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'ice_slugW')) {
                    $table->string('ice_slug')->default('');
                }
            });

        }
        foreach ($fields as $field) {
            $data = [
                'name' => $field['name'],
                'label' => ucwords(preg_replace('/([^A-Z])([A-Z])/', '$1 $2', $field['name'])),
                'module_id' => $moduleId,
            ];

            [$data['input_type'], $data['data_type']] = $this->getInputType($field);

            if ($data['input_type'] == 'decimal') {
                $data['decimal_places'] = $field['decimals'];
            }

            if (isset($field['length'])) {
                $data['field_length'] = $field['length'];
            }

            if ($field['type'] == 'CHAR') {
                $data['field_length'] = 1;
            }

            if ($field['type'] == 'VARCHAR') {
                $data['validation'] = 'max:'.$field['length'];
            }
            if (isset($data['required'])) {

                if (strlen($data['validation']) > 0) {
                    $data['validation'] .= '|';
                }
                $data['validation'] .= 'require';
            }


            if (! in_array(strtolower($field['name']), $this->excludeFields)) {
                Field::insert(Field::getField($data, $cnt++));
            }


        }

    }

    public function createRelationship($module_id, $name, $data)
    {

        foreach ($data as $item) {
            $item['ref_table'] = $this->prefix.$item['ref_table'];
            if ($item['type'] == 'FOREIGN') {
                if ($item['ref_table'] != $name) {

                    $relationship_id = Relationship::insertGetId([
                        'name' => strtolower($name).'_'.strtolower($item['ref_table']),
                        'modules' => implode(',', [
                            $module_id,
                            Module::where('name', strtolower($item['ref_table']))->first()->id,
                        ]),
                        'related_field_types' => 'integer,integer',
                    ]);

                    return [$relationship_id, $name, $item['ref_table']];
                }
            }
        }
    }

    public function createModuleSubpanels($moduleId, $relationshipId, $primary_name, $secondary_name, $fields)
    {

        $id = ModuleSubpanel::insertGetId([
            'name' => $primary_name.'_'.$secondary_name,
            'label' => 'Contacts',
            'relationship_id' => $relationshipId,
            'module_id' => $moduleId,
        ]);
        foreach ($fields as $field) {
            SubpanelField::insert([
                'subpanel_id' => $id,
                'field_id' => $field->id,
            ]);
        }
    }

    public function createModuleGroup($name, $label)
    {
        return ModuleGroup::insertGetId([
            'name' => strtolower($name),
            'label' => ucwords(preg_replace('/([^A-Z])([A-Z])/', '$1 $2',
                str_replace('_', ' ', $label)
            )),
            'view_order' => 0,
        ]);
    }

    public function createModule($name, $label, $order, $group_id, $primary_field="id")
    {
        return Module::insertGetId([
            'name' => $name,
            'label' => ucwords(preg_replace('/([^A-Z])([A-Z])/', '$1 $2', $label)),
            'description' => ucwords(preg_replace('/([^A-Z])([A-Z])/', '$1 $2',
                    str_replace('_', ' ', $label)
                )).
                ' module',
            'view_order' => $order,
            'module_group_id' => $group_id,
            'icon' => $this->getIcon($order),
            'faker_seed' => 0,
            'create_table' => 0,
            'status' => 1,
            'primary_field' => $primary_field,
        ]);

    }

    private function addModulesAndRoles()
    {
        $module = \App\Models\Iceburg\Module::where('name', 'roles')->first();
        $records = DB::table($module->name)->get();
        Permission::truncate();
        foreach ($records as $record) {
            Module::all()->each(function ($module) use ($record) {
                Permission::insert([
                    'role_id' => $record->id,
                    'module_id' => $module->id,
                ]);
            });
        }
    }

    private function addDataletTypes()
    {
        DataletType::truncate();
        DataletType::insert([
            ['id' => 1, 'name' => 'Doughnut Chart'],
            ['id' => 2, 'name' => 'Line Chart'],
            ['id' => 3, 'name' => 'Bar Graph'],
            ['id' => 4, 'name' => 'Pie Chart'],
            ['id' => 5, 'name' => 'Area Chart'],
            ['id' => 6, 'name' => 'Latest Campaign'],
            ['id' => 7, 'name' => 'Latest Meetings'],
        ]);
    }

    private function addDataLets()
    {
        Datalet::truncate();
        Datalet::insert([

            ['type' => 1,
                'module_id' => 0,
                'label' => 'Total Sales',
                'size' => 12,
                'display_order' => 1],
            ['type' => 2,
                'module_id' => 0,
                'label' => 'Number of new Leads / Contacts / Accounts over the last 7 Days',
                'size' => 12,
                'display_order' => 1],
            ['type' => 3,
                'module_id' => 0,
                'label' => 'Meetings',
                'size' => 12,
                'display_order' => 1],
            ['type' => 4,
                'module_id' => 0,
                'label' => 'Number of new Opportunities / Quotes / Contracts over the last 7 Days',
                'size' => 12,
                'display_order' => 1],
            ['type' => 1,
                'module_id' => 0,
                'label' => 'Orders This Month',
                'size' => 12,
                'display_order' => 1],
        ]);

    }

    private function addWorkflowActions()
    {
        WorkflowAction::truncate();
        WorkflowAction::insert(
            [
                ['name' => 'Insert new Module Record'],
                ['name' => 'Insert new Relationship Record'],
                ['name' => 'Update Module Record'],
                ['name' => 'Update Relationship Record'],
                ['name' => 'Delete Module Record'],
                ['name' => 'Delete Relationship Record'],
                ['name' => 'Field Change Status'],
            ]
        );
    }

    private function AddUsers()
    {
        User::truncate();
        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('admin'),
            'role_id' => 1,
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('users')->insertGetId([
            'name' => 'User',
            'email' => 'user@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('user'),
            'role_id' => 2,
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('users')->insertGetId([
            'name' => 'Sales',
            'email' => 'sales@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('sales'),
            'role_id' => 3,
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('users')->insertGetId([
            'name' => 'Accounting',
            'email' => 'accounting@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('accounting'),
            'role_id' => 4,
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('users')->insertGetId([
            'name' => 'Marketing',
            'email' => 'marketing@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('marketing'),
            'role_id' => 5,
        ]);
    }

    private function AddSettings()
    {

        DB::table('settings')->insert([
            'name' => 'theme',
            'value' => 'light',
        ]);

        DB::table('settings')->insert([
            'name' => 'search_per_page',
            'value' => '10',
        ]);

        DB::table('settings')->insert([
            'name' => 'submodule_search_per_page',
            'value' => '10',
        ]);

        DB::table('settings')->insert([
            'name' => 'title',
            'value' => 'Iceburg CRM',
        ]);

        DB::table('settings')->insert([
            'name' => 'description',
            'value' => 'Open Source, data driven, extendable, unlimited relationships, convertable modules, 29 default themes, light/dark themes',
        ]);

        DB::table('settings')->insert([
            'name' => 'max_export_records',
            'value' => 10000,
        ]);

    }

    private function Roles()
    {
        return [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'User'],
            ['id' => 3, 'name' => 'Sales'],
            ['id' => 4, 'name' => 'Accounting'],
            ['id' => 5, 'name' => 'Support'],
            ['id' => 6, 'name' => 'Marketing'],
            ['id' => 7, 'name' => 'HR'],
        ];
    }

    private function createBaseTables()
    {
        //Schema::drop('roles');
        //  DB::statement('create table roles (name varchar(200))');
        $moduleId = Module::insertGetId([
            'name' => 'roles',
            'label' => 'Roles',
            'description' => 'Roles',
            'view_order' => 0,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1,
        ]);

        Field::insert(Field::getField([
            'name' => 'name',
            'label' => 'Name',
            'module_id' => $moduleId,
            'field_length' => 64,
            'input_type' => 'text',
        ], 0));

    }
}
