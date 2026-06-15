<?php

namespace Database\Seeders\Core;

use App\Models\Module;
use App\Models\ModuleGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ModuleGroup::truncate();
        Module::truncate();

        $this->seedModuleGroups();
        $this->seedModules();

    }

    private function seedModuleGroups()
    {

        ModuleGroup::insert([
            'id' => '6',
            'name' => 'admin',
            'label' => 'Admin',
            'view_order' => 0,
        ]);
    }

    private function seedModules()
    {

        $order = 0;
        Module::insert([
            'name' => 'ice_users',
            'label' => 'Users',
            'description' => 'Users',
            'view_order' => $order++,
            'module_group_id' => 6,
            'icon' => 'UserPlusIcon',
            'faker_seed' => 0,
            'create_table' => 0,
            'status' => 1,
            'primary' => 1,
        ]);


        Module::insert([
            'name' => 'ice_roles',
            'label' => 'Roles',
            'description' => 'Roles',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 0,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'ice_themes',
            'label' => 'Themes',
            'description' => 'Themes',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1,
            'create_table' => 0,
            'primary' => 1,
        ]);


        Module::insert([
            'name' => 'ice_modules',
            'label' => 'Modules',
            'description' => 'System Modules',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 0,
            'admin' => 1,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'ice_fields',
            'label' => 'Fields',
            'description' => 'System Fields',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 0,
            'admin' => 1,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'ice_relationships',
            'label' => 'Relationships',
            'description' => 'Relationships',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 0,
            'admin' => 1,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'ice_module_subpanels',
            'label' => 'Subpanels',
            'description' => 'System Subpanel',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 0,
            'admin' => 1,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'ice_datalets',
            'label' => 'Datalets',
            'description' => 'System Datalets',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 0,
            'admin' => 1,
            'status' => 1,
            'primary' => 1,
        ]);

        Log::info('Modules Seeding Complete');
    }
}
