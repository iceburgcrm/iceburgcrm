<?php

namespace Database\Seeders\Ai;


use App\Models\Module;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Log;

class ModuleSeeder extends Seeder
{
    /**
     * Generates tables, fields and data lists.
     *
     * @return void
     */
    public function run()
    {
        $order=Module::max('view_order');
        $countriesId = Module::insertGetId([
            'name' => 'countries',
            'label' => 'Countries',
            'description' => 'Countries',
            'icon' => 'GlobeAltIcon',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1,
            'primary' => 1,
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
            'primary' => 1,
        ]);

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
            'primary' => 1,
        ]);


    }


}
