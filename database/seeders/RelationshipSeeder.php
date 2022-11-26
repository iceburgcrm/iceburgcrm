<?php
// add comments to the code

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\Relationship;
use App\Models\Module;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Schema as Schema;

class RelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Relationship::truncate();
        Relationship::insert([
            'name' => 'accounts_contacts',
            'modules' => implode(",", [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'contacts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'accounts_opportunities',
            'modules' => implode(",", [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'opportunities')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'leads_accounts_opportunities',
            'modules' => implode(",", [
                Module::where('name', 'leads')->first()->id,
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'opportunities')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'accounts_cases',
            'modules' => implode(",", [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'cases')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'accounts_contracts',
            'modules' => implode(",", [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'contracts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'accounts_meetings',
            'modules' => implode(",", [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'meetings')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'opportunities_contacts',
            'modules' => implode(",", [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'opportunities')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'opportunities_cases',
            'modules' => implode(",", [
                Module::where('name', 'opportunities')->first()->id,
                Module::where('name', 'cases')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'opportunities_contracts',
            'modules' => implode(",", [
                Module::where('name', 'opportunities')->first()->id,
                Module::where('name', 'contracts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'opportunities_meetings',
            'modules' => implode(",", [
                Module::where('name', 'opportunities')->first()->id,
                Module::where('name', 'meetings')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'user_meetings',
            'modules' => implode(",", [
                Module::where('name', 'users')->first()->id,
                Module::where('name', 'meetings')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

          Relationship::insert([
                'name' => 'contracts_lineitems',
                'modules' => implode(",", [
                    Module::where('name', 'contracts')->first()->id,
                    Module::where('name', 'lineitems')->first()->id,
                ]),
                'related_field_types' => 'integer,integer',
            ]);

        Relationship::insert([
            'name' => 'users_tasks',
            'modules' => implode(",", [
                Module::where('name', 'users')->first()->id,
                Module::where('name', 'tasks')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'users_tasks',
            'modules' => implode(",", [
                Module::where('name', 'users')->first()->id,
                Module::where('name', 'tasks')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'projects_tasks',
            'modules' => implode(",", [
                Module::where('name', 'projects')->first()->id,
                Module::where('name', 'tasks')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'campaigns_tasks',
            'modules' => implode(",", [
                Module::where('name', 'campaigns')->first()->id,
                Module::where('name', 'tasks')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'documents_tasks',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'tasks')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'documents_accounts',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'accounts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'documents_opportunities',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'opportunities')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'documents_cases',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'cases')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'documents_contracts',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'contracts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'documents_meetings',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'meetings')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'documents_tasks',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'tasks')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'documents_users',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'users')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);


        Relationship::insert([
            'name' => 'documents_tasks',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'tasks')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'notes_accounts',
            'modules' => implode(",", [
                Module::where('name', 'notes')->first()->id,
                Module::where('name', 'accounts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'notes_opportunities',
            'modules' => implode(",", [
                Module::where('name', 'notes')->first()->id,
                Module::where('name', 'opportunities')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'notes_cases',
            'modules' => implode(",", [
                Module::where('name', 'documents')->first()->id,
                Module::where('name', 'cases')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'notes_contracts',
            'modules' => implode(",", [
                Module::where('name', 'notes')->first()->id,
                Module::where('name', 'contracts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'notes_meetings',
            'modules' => implode(",", [
                Module::where('name', 'notes')->first()->id,
                Module::where('name', 'meetings')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'notes_tasks',
            'modules' => implode(",", [
                Module::where('name', 'notes')->first()->id,
                Module::where('name', 'tasks')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'notes_users',
            'modules' => implode(",", [
                Module::where('name', 'notes')->first()->id,
                Module::where('name', 'users')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'groups_accounts',
            'modules' => implode(",", [
                Module::where('name', 'groups')->first()->id,
                Module::where('name', 'accounts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'projects_accounts',
            'modules' => implode(",", [
                Module::where('name', 'projects')->first()->id,
                Module::where('name', 'accounts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'campaigns_accounts',
            'modules' => implode(",", [
                Module::where('name', 'campaigns')->first()->id,
                Module::where('name', 'accounts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'groups_accounts',
            'modules' => implode(",", [
                Module::where('name', 'groups')->first()->id,
                Module::where('name', 'accounts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'opportunities_quotes',
            'modules' => implode(",", [
                Module::where('name', 'opportunities')->first()->id,
                Module::where('name', 'quotes')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'opportunities_quotes_accounts',
            'modules' => implode(",", [
                Module::where('name', 'opportunities')->first()->id,
                Module::where('name', 'quotes')->first()->id,
                Module::where('name', 'accounts')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'accounts_invoices',
            'modules' => implode(",", [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'invoices')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'accounts_invoices_users',
            'modules' => implode(",", [
                Module::where('name', 'accounts')->first()->id,
                Module::where('name', 'invoices')->first()->id,
                Module::where('name', 'users')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'modules_fields',
            'modules' => implode(",", [
                Module::where('name', 'modules')->first()->id,
                Module::where('name', 'fields')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'modules_subpanels',
            'modules' => implode(",", [
                Module::where('name', 'modules')->first()->id,
                Module::where('name', 'module_subpanels')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);

        Relationship::insert([
            'name' => 'modules_datalets',
            'modules' => implode(",", [
                Module::where('name', 'modules')->first()->id,
                Module::where('name', 'datalets')->first()->id,
            ]),
            'related_field_types' => 'integer,integer',
        ]);


        $relationship = new Relationship();
        $relationship->generate(6);

        Log::info("Relationship Seeding Complete");
    }
}

