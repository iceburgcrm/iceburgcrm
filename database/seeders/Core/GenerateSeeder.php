<?php

namespace Database\Seeders\Core;

use App\Models\Connector;
use App\Models\ConnectorCommand;
use App\Models\Datalet;
use App\Models\DataletType;
use App\Models\Endpoint;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User
use App\Models\IceHelp;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Log;


use Database\Seeders\Core\TemplateConnectorSeeder as TemplateConnectorSeeder;

class GenerateSeeder extends Seeder
{
    /**
     * Generates tables, fields and data lists.
     *
     * @return void
     */
    public function run()
    {
        $seedAmount = 50;
        Log::info('Generating users');
        $this->addUsers();
        $this->addThemes();
        $this->addRoles();
        $this->addSettings();
        $this->addDataletTypes();
        $this->addDatalets();

        Log::info('Generating connectors');
        $this->addCustomConnectors();
        $this->addTemplateConnectors();

        Log::info('Add Workflow Actions');
       // $this->addWorkflowActions();

        Log::info('Add Datalet Types');
        #$this->addDataletTypes();
        #$this->addDatalets();

        Log::info('Add Datalet Types');
        $this->addHelp();

        Log::info('Generating module');
        $module = new Module;
        $module->generate($seedAmount);

        Log::info('Generating static lists');

        /*
         $faker = Factory::create();
        $current = $this;
        Module::where('status', 1)
            ->where('create_table', 1)
            ->where('faker_seed', 0)
            ->get()
            ->each(function ($module) use ($current) {
                if (method_exists($current, $module->name)) {
                    Log::info('Generating module: '.$module->name);
                    $table = strtolower($module->name);
                    DB::table($table)->truncate();
                    $data = $current->{$module->name}();
                    foreach ($data as $row) {
                        $row['ice_slug'] = bin2hex(random_bytes(16));
                        DB::table($table)->insert($row);
                    }
                }
                Module::where('id', $module->id)
                        ->update(['create_table' => 0]);
            });
        */

        $this->addModulesAndRoles();
    }



    private function addModulesAndRoles()
    {
        $module = Module::where('name', 'ice_roles')->first();
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
            ['id' => 7, 'name' => 'CRM Stats'],
            ['id' => 8, 'name' => 'Totals Report'],
        ]);
    }

    private function addDataLets()
    {
        Datalet::truncate();
        Datalet::insert([

            ['type' => 7,
                'module_id' => 2,
                'label' => 'CRM Stats',
                'size' => 12,
                'display_order' => 12],
            ['type' => 8,
                'module_id' => 1,
                'label' => 'Totals Report',
                'size' => 12,
                'display_order' => 6],
        ]);

    }

    private function addWorkflowActions()
    {
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

    private function addThemes()
    {
        Theme::truncate();
        Theme::insert(
            [
                ['name' => 'iceburgsaas'],
                ['name' => 'iceburgcorporate'],
                ['name' => 'iceburgai'],
                ['name' => 'light'],
                ['name' => 'dark'],
                ['name' => 'cupcake'],
                ['name' => 'bumblebee'],
                ['name' => 'emerald'],
                ['name' => 'corporate'],
                ['name' => 'synthwave'],
                ['name' => 'retro'],
                ['name' => 'cyberpunk'],
                ['name' => 'valentine'],
                ['name' => 'halloween'],
                ['name' => 'garden'],
                ['name' => 'forest'],
                ['name' => 'aqua'],
                ['name' => 'lofi'],
                ['name' => 'pastel'],
                ['name' => 'fantasy'],
                ['name' => 'wireframe'],
                ['name' => 'black'],
                ['name' => 'luxury'],
                ['name' => 'dracula'],
                ['name' => 'cmyk'],
                ['name' => 'autumn'],
                ['name' => 'business'],
                ['name' => 'acid'],
                ['name' => 'lemonade'],
                ['name' => 'night'],
                ['name' => 'coffee'],
                ['name' => 'winter'],
            ]
        );
    }


    private function addRoles()
    {
        Role::truncate();
        Role::insert(
            [
                ['id' => 1, 'name' => 'Admin'],
                ['id' => 2, 'name' => 'User'],
                ['id' => 3, 'name' => 'Sales'],
                ['id' => 4, 'name' => 'Accounting'],
                ['id' => 5, 'name' => 'Support'],
                ['id' => 6, 'name' => 'Marketing'],
                ['id' => 7, 'name' => 'HR'],
            ]
        );
    }


    private function addUsers()
    {
        User::truncate();
        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('ice_users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('admin'),
            'role_id' => 1,
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('ice_users')->insertGetId([
            'name' => 'User',
            'email' => 'user@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('user'),
            'role_id' => 2,
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('ice_users')->insertGetId([
            'name' => 'Sales',
            'email' => 'sales@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('sales'),
            'role_id' => 3,
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('ice_users')->insertGetId([
            'name' => 'Accounting',
            'email' => 'accounting@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('accounting'),
            'role_id' => 4,
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000'.rand(10, 99).'.jpg');
        $userId = DB::table('ice_users')->insertGetId([
            'name' => 'Marketing',
            'email' => 'marketing@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,'.base64_encode($image),
            'password' => bcrypt('marketing'),
            'role_id' => 5,
        ]);
    }

    private function addSettings()
    {
        Log::info('Generating Settings');
        Setting::insert([
            'name' => 'theme',
            'value' => 'corporate',
        ]);

        Setting::insert([
            'name' => 'search_per_page',
            'value' => '10',
        ]);

        Setting::insert([
            'name' => 'submodule_search_per_page',
            'value' => '10',
        ]);

        Setting::insert([
            'name' => 'title',
            'value' => 'Iceburg CRM',
        ]);

        Setting::insert([
            'name' => 'description',
            'value' => 'Open Source, data driven, extendable, unlimited relationships, convertable modules, 29 default themes, light/dark themes',
        ]);

        Setting::insert([
            'name' => 'max_export_records',
            'value' => 10000,
        ]);

        Setting::insert([
            'name' => 'welcome_popup',
            'value' => true,
        ]);

        Setting::insert([
            'name' => 'logo',
            'value' => '',
        ]);

        Setting::insert([
            'name' => 'language',
            'value' => 'en',
        ]);


        Setting::insert([
            'name' => 'help',
            'value' => 'true',
        ]);

    }


    private function addCustomConnectors()
    {

        $jokesConnectorId = Connector::insertGetId([
            'name'      => 'Joke of the Day',
            'base_url'  => 'https://official-joke-api.appspot.com',
            'auth_type' => 'None',
            'type'      => 2, // custom connector
            'status'    => 1,
        ]);

        $tenJokesEndpointId = Endpoint::insertGetId([
            'connector_id' => $jokesConnectorId,
            'endpoint'     => '/random_ten',
            'request_type' => 'GET',
            'params'       => json_encode([]),
            'headers'      => json_encode([]),
            'status'       => 1,
        ]);


        ConnectorCommand::insert([
            [
                'connector_id' => $jokesConnectorId,
                'name'         => 'Random Joke',
                'class_name'   => 'jokes',
                'method_name'  => 'joke_without_endpoint',
                'description'  => 'Get a random joke',
                'status'       => 1,
            ],
            [
                'connector_id' => $jokesConnectorId,
                'name'         => 'Random 10 Jokes',
                'class_name'   => 'jokes',
                'method_name'  => 'random_ten',
                'endpoint_id'  => $tenJokesEndpointId,
                'description'  => 'Gives you 10 random jokes',
                'status'       => 1,
            ],
            [
                'connector_id' => $jokesConnectorId,
                'name'         => 'Random 10 Jokes with mapping',
                'class_name'   => 'jokes',
                'method_name'  => 'random_ten_with_mapping',
                'endpoint_id'  => $tenJokesEndpointId,
                'description'  => 'Gives you 10 random jokes and saves them to a module',
                'status'       => 1,
            ],
        ]);


        // --- Custom Connector: IceburgCRM ---
        $iceburgConnectorId = Connector::insertGetId([
            'name'      => 'IceburgCRM',
            'base_url'  => 'http://localhost',
            'auth_type' => 'Basic Auth',
            'username'  => 'admin@iceburg.ca',
            'password'  => 'admin',
            'type'      => 2, // custom connector
            'status'    => 1,
        ]);

        ConnectorCommand::insert([
            'connector_id' => $iceburgConnectorId,
            'name'         => 'Backup Contacts',
            'class_name'   => 'iceburg',
            'description'  => 'This method will backup the last 10 contacts to another IceburgCRM instance',
            'method_name'  => 'backup_contacts',
            'status'       => 1,
        ]);

        ConnectorCommand::insert([
            'connector_id' => $iceburgConnectorId,
            'name'         => 'Backup Accounts',
            'class_name'     => 'iceburg',
            'description'  => 'This method will backup the last 10 accounts to another IceburgCRM instance',
            'method_name'  => 'backup_accounts',
            'status'       => 1,
        ]);
    }

    private function addTemplateConnectors()
    {
        $this->call(TemplateConnectorSeeder::class);
    }

    private addhelp()
    {
        IceHelp::insert([
            'slug' => 'dashboard',
            'content' => 'This is the dashboard, this is the first page you will see when you login in.  Below are datalets.  They are generic widget components that can be defined to display whatever type of data you wish.<br><br>
                You can change the order, disable, change the name in the <a href="http://localhost:8080/module/ice_datalets">Datalet admin page</a>.<br><br>
                Datalets data can be defined in the code <a href="https://github.com/iceburgcrm/iceburgcrm/blob/main/app/Models/Datalet.php">here</a> 
                and you can define the <a href="https://github.com/iceburgcrm/iceburgcrm/blob/main/resources/js/Components/Datalet.vue">display type here</a>'
        ]);

        IceHelp::insert([
            'slug' => 'allmodules',
            'content' => 'This section shows a list of all modules currently available.  Modules are filtered by role so only modules you have access to will appear here.'
        ]);

        IceHelp::insert([
            'slug' => 'module_search',
            'content' => 'This section allows you to search through a module. <br><br>The export button at the top of the page will export all records.  You can select individual records in the dropdown below to export or delete.  You can export in 6 different formats.  Exporting will take related fields and convert the ids to the content the field is connected to.<br><br>Import allows you to import records, the format must follow the same format as exporting.  Importing will take related field values and convert them into the ids that link them automatically.<br><br>Selecting convert will convert to your record for your workflow which will be displayed in the module record detail page.'
        ]);

        IceHelp::insert([
            'slug' => 'import',
            'content' => 'This section allows you to import data. <br><br>The format must follow the same format as exporting.  Importing will take related field values and convert them into the ids that link them automatically.  You have the option of removing the header through the checkbox below.'
        ]);


        IceHelp::insert([
            'slug' => 'audit_log',
            'content' => 'This section will show you who performed what action on what record.  There are 4 actions.  Read, write, import and export.'
        ]);

        IceHelp::insert([
            'slug' => 'permissions',
            'content' => 'This section will allow you to set module permissions.  There are 4 types of permissions.  Read, write, import and export.'
        ]);


        IceHelp::insert([
            'slug' => 'add_module_record',
            'content' => 'This section will allow you to add new records to this module.  Using the AI Assist button will send over all of the fields and types and let the AI suggest data to fill in the record with.'
        ]);


        IceHelp::insert([
            'slug' => 'settings',
            'content' => 'This page will let you change global settings of the crm.  From color changes, to turning off help to setting the number of records per module or changing your language.'
        ]);


        IceHelp::insert([
            'slug' => 'workflow',
            'content' => 'This page will allow you to change your workflow.  Select which module you want another module to convert to.  Together they allow you to convert records from one module type to another and to see where the record came from and where it needs to where it currently sits in your workflow.  A typical workflow would have leads convert to contacts.  Contacts to accounts, to opportunities to line items.'
        ]);


        IceHelp::insert([
            'slug' => 'admin_data',
            'content' => 'This page will let you reset the data in the crm.  Be careful because all data changes will be lost.'
        ]);



        IceHelp::insert([
            'slug' => 'connectors',
            'content' => 'This section allows you to create connectors, that are connected to endpoints and connector commands.<br><br>As a sample use the first api, the joke api'
        ]);


        IceHelp::insert([
            'slug' => 'connector',
            'content' => 'This section allows you to create endpoints for your connector, allows you to set the connector settings and create connector commands<br><br>Commands are linked to a specific class, method that will allow you to map.  Check out the free jokes connector and methods for examples.'
        ]);




 

    }


       



}
