<?php

namespace Database\Seeders\Core;

use App\Models\Connector;
use App\Models\Datalet;
use App\Models\DataletType;
use App\Models\ConnectorCommand;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Theme;
use App\Models\User;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Log;

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
        $this->addConnectors();

        Log::info('Add Workflow Actions');
       // $this->addWorkflowActions();

        Log::info('Add Datalet Types');
        #$this->addDataletTypes();
        #$this->addDatalets();

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

    }

    private function addConnectors()
    {
        $connectorId = Connector::insertGetId([
            'name' => 'joke of the day',
            'class' => 'jokes',
            'base_url' => 'https://official-joke-api.appspot.com',
            'auth_type' => 'None',
        ]);

        ConnectorCommand::insert([
            'connector_id' => $connectorId,
            'name' => 'Random Joke',
            'description' => 'Get a random jokei',
            'method_name' => 'random_joke',
        ]);

        $connectorId = Connector::insertGetId([
            'name' => 'IceburgCRM',
            'class' => 'iceburg',
            'base_url' => 'http://localhost',
            'auth_type' => 'Basic Auth',
            'username' => 'admin@iceburg.ca',
            'password' => 'admin',
        ]);

        ConnectorCommand::insert([
            'connector_id' => $connectorId,
            'name' => 'Backup Contacts',
            'description' => 'This method will backup the last 10 contacts to another IceburgCRM instance',
            'method_name' => 'backup_contacts',
        ]);

        ConnectorCommand::insert([
            'connector_id' => $connectorId,
            'name' => 'Backup Accounts',
            'description' => 'This method will backup the last 10 accounts to another IceburgCRM instance',
            'method_name' => 'backup_accounts',
        ]);

    }


}
