<?php

// add comments to the code

namespace Database\Seeders\Custom;

use App\Models\Field;
use App\Models\Module;
use App\Models\ModuleSubpanel;
use App\Models\Relationship;
use App\Models\SubpanelField;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ModuleSubpanelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ModuleSubpanel::truncate();
        SubpanelField::truncate();
/*
        $id = ModuleSubpanel::insertGetId([
            'name' => 'accounts_contacts',
            'label' => 'Contacts',
            'relationship_id' => Relationship::where('name', 'accounts_contacts')
                ->orWhere('name', 'contacts_accounts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contacts')->first()->id
            )
                ->where('name', 'profile_pic')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contacts')->first()->id
            )
                ->where('name', 'first_name')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contacts')->first()->id
            )
                ->where('name', 'last_name')->first()->id,
        ]);
*/


        Log::info('ModuleSubpanel Seeding Complete');
    }
}
