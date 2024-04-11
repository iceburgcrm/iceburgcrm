<?php

// add comments to the code

namespace Database\Seeders\Custom;

use App\Models\Module;
use App\Models\Relationship;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /***
         * examples
         */
    /*
        Relationship::truncate();
        Relationship::insert([
            'name' => 'accounts_contacts',
            'modules' => implode(',', [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'contacts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'accounts_opportunities',
            'modules' => implode(',', [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'opportunities')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'leads_accounts_opportunities',
            'modules' => implode(',', [
                Module::where('name', 'leads')->first()->id,
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'opportunities')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);
    */


        Log::info('Relationship Seeding Complete');
    }
}
