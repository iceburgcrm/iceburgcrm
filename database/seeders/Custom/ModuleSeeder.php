<?php

namespace Database\Seeders\Custom;

use App\Models\Module;
use App\Models\ModuleConvertable;
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
        ModuleConvertable::truncate();

        $this->seedModuleGroups();
        $this->seedModules();
        $this->seedConvertedModules();
    }

    private function seedConvertedModules()
    {
        /*
        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'leads')->first()->id,
            'module_id' => Module::where('name', 'contacts')->first()->id,
            'level' => 1,
        ]);
        */

    }

    private function seedModuleGroups()
    {
        /*
        ModuleGroup::insert([
            'id' => '1',
            'name' => 'companies',
            'label' => 'Companies',
            'view_order' => 0,
        ]);
        */

    }

    private function seedModules()
    {
        $order=0;
        Module::insert([
            'name' => 'currency',
            'label' => 'Currency',
            'description' => 'Currency',
            'view_order' => $order++,
            'icon' => 'CurrencyPoundIcon',
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        $countriesId = Module::insertGetId([
            'name' => 'countries',
            'label' => 'Countries',
            'description' => 'Countries',
            'icon' => 'GlobeAltIcon',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'states',
            'label' => 'States',
            'description' => 'States',
            'view_order' => $order++,
            'icon' => 'GlobeAmericasIcon',
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1,
        ]);


        Log::info('Modules Seeding Complete');
    }
}
