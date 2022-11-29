<?php
// add comments to the code

namespace Database\Seeders;

use App\Models\SubpanelField;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\ModuleSubpanel;
use App\Models\Module;
use App\Models\Field;
use App\Models\Relationship;

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

        $id=ModuleSubpanel::insertGetId([
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
                ->where('name', 'first_name')->first()->id
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contacts')->first()->id
            )
                ->where('name', 'last_name')->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'leads_accounts_opportunities',
            'label' => 'Leads and Opportunities',
            'relationship_id' => Relationship::where('name', 'leads_accounts_opportunities')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);

        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contacts')->first()->id
            )
            ->where('name', 'first_name')->first()->id
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contacts')->first()->id
            )
                ->where('name', 'last_name')->first()->id
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contacts')->first()->id
            )
                ->where('name', 'description')->first()->id
        ]);



        $id=ModuleSubpanel::insertGetId([
            'name' => 'contacts_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'accounts_contacts')
                ->orWhere('name', 'contacts_accounts')->first()->id,
            'module_id' => Module::where('name', 'contacts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contacts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_opportunities',
            'label' => 'Opportunities',
            'relationship_id' => Relationship::where('name', 'accounts_opportunities')
                ->orWhere('name', 'opportunities_accounts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'opportunities')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'opportunities_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'accounts_opportunities')
                ->orWhere('name', 'opportunities_accounts')->first()->id,
           'module_id' => Module::where('name', 'opportunities')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_cases',
            'label' => 'Cases',
            'relationship_id' => Relationship::where('name', 'accounts_cases')
                ->orWhere('name', 'cases_accounts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'cases')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'cases_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'accounts_cases')
                ->orWhere('name', 'cases_accounts')->first()->id,
            'module_id' => Module::where('name', 'cases')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_meetings',
            'label' => 'Meetings',
            'relationship_id' => Relationship::where('name', 'accounts_meetings')
                ->orWhere('name', 'meetings_accounts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'meetings')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'meetings_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'accounts_meetings')
                ->orWhere('name', 'meetings_accounts')->first()->id,
            'module_id' => Module::where('name', 'meetings')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_notes',
            'label' => 'Notes',
            'relationship_id' => Relationship::where('name', 'accounts_notes')
                ->orWhere('name', 'notes_accounts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'notes')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'notes_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'accounts_notes')
                ->orWhere('name', 'notes_accounts')->first()->id,
            'module_id' => Module::where('name', 'notes')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_documents',
            'label' => 'Documents',
            'relationship_id' => Relationship::where('name', 'accounts_documents')
                ->orWhere('name', 'documents_accounts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'documents')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'documents_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'accounts_documents')
                ->orWhere('name', 'documents_accounts')->first()->id,
            'module_id' => Module::where('name', 'documents')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_notes',
            'label' => 'Notes',
            'relationship_id' => Relationship::where('name', 'accounts_notes')
                ->orWhere('name', 'notes_accounts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'notes')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'notes_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'accounts_notes')
                ->orWhere('name', 'notes_accounts')->first()->id,
            'module_id' => Module::where('name', 'notes')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);
/*
        $id=ModuleSubpanel::insertGetId([
            'name' => 'leads_opportunities',
            'label' => 'Opportunities',
            'relationship_id' => Relationship::where('name', 'leads_opportunities')
                ->orWhere('name', 'opportunities_leads')->first()->id,
            'subpanel_fields' => 'opportunities.id,opportunities.name',
            'module_id' => Module::where('name', 'Leads')->first()->id,
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'opportunities_leads',
            'label' => 'Leads',
            'relationship_id' => Relationship::where('name', 'leads_opportunities')
                ->orWhere('name', 'opportunities_leads')->first()->id,
            'subpanel_fields' => 'leads.id,leads.name',
            'module_id' => Module::where('name', 'Opportunities')->first()->id,
        ]);


        $id=ModuleSubpanel::insertGetId([
            'name' => 'projects_notes',
            'label' => 'Notes',
            'relationship_id' => Relationship::where('name', 'projects_notes')
                ->orWhere('name', 'notes_projects')->first()->id,
            'subpanel_fields' => 'notes.id,notes.subject',
            'module_id' => Module::where('name', 'Projects')->first()->id,
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'notes_projects',
            'label' => 'Projects',
            'relationship_id' => Relationship::where('name', 'projects_notes')
                ->orWhere('name', 'notes_projects')->first()->id,
            'subpanel_fields' => 'projects.id,projects.name',
            'module_id' => Module::where('name', 'Notes')->first()->id,
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'projects_tasks',
            'label' => 'Tasks',
            'relationship_id' => Relationship::where('name', 'projects_tasks')
                ->orWhere('name', 'tasks_projects')->first()->id,
            'subpanel_fields' => 'tasks.id,tasks.name',
            'module_id' => Module::where('name', 'Projects')->first()->id,
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'tasks_projects',
            'label' => 'Projects',
            'relationship_id' => Relationship::where('name', 'projects_tasks')
                ->orWhere('name', 'tasks_projects')->first()->id,
            'subpanel_fields' => 'projects.id,projects.name',
            'module_id' => Module::where('name', 'Tasks')->first()->id,
        ]);

*/

        $id=ModuleSubpanel::insertGetId([
            'name' => 'projects_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'projects_accounts')
                ->orWhere('name', 'accounts_projects')->first()->id,
            'module_id' => Module::where('name', 'projects')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_projects',
            'label' => 'Projects',
            'relationship_id' => Relationship::where('name', 'projects_accounts')
                ->orWhere('name', 'accounts_projects')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'projects')->first()->id
            )->first()->id
        ]);
/*
        $id=ModuleSubpanel::insertGetId([
            'name' => 'contracts_lineitems',
            'label' => 'Line Items',
            'relationship_id' => Relationship::where('name', 'contracts_lineitems')
                ->orWhere('name', 'lineitems_contracts')->first()->id,
            'subpanel_fields' => 'lineitems.id,lineitems.name',
            'module_id' => Module::where('name', 'Contracts')->first()->id,
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'lineitems_contracts',
            'label' => 'Contracts',
            'relationship_id' => Relationship::where('name', 'contracts_lineitems')
                ->orWhere('name', 'lineitems_contracts')->first()->id,
            'subpanel_fields' => 'contracts.id,contracts.name',
            'module_id' => Module::where('name', 'Line Items')->first()->id,
        ]);
*/

        $id=ModuleSubpanel::insertGetId([
            'name' => 'contracts_notes',
            'label' => 'Notes',
            'relationship_id' => Relationship::where('name', 'contracts_notes')
                ->orWhere('name', 'notes_contracts')->first()->id,
            'module_id' => Module::where('name', 'contracts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'notes')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'notes_contracts',
            'label' => 'Contracts',
            'relationship_id' => Relationship::where('name', 'contracts_notes')
                ->orWhere('name', 'notes_contracts')->first()->id,
            'module_id' => Module::where('name', 'notes')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'contracts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_invoices',
            'label' => 'Tasks',
            'relationship_id' => Relationship::where('name', 'accounts_invoices')
                ->orWhere('name', 'invoices_accounts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'invoices')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'invoices_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'accounts_invoices')
                ->orWhere('name', 'invoices_accounts')->first()->id,
            'module_id' => Module::where('name', 'invoices')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'opportunities_quotes',
            'label' => 'Quotes',
            'relationship_id' => Relationship::where('name', 'opportunities_quotes')
                ->orWhere('name', 'quotes_opportunities')->first()->id,
            'module_id' => Module::where('name', 'opportunities')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'quotes')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'groups_accounts',
            'label' => 'Accounts',
            'relationship_id' => Relationship::where('name', 'groups_accounts')
                ->orWhere('name', 'accounts_groups')->first()->id,
            'module_id' => Module::where('name', 'groups')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'accounts')->first()->id
            )->first()->id
        ]);

        $id=ModuleSubpanel::insertGetId([
            'name' => 'accounts_groups',
            'label' => 'Groups',
            'relationship_id' => Relationship::where('name', 'groups_accounts')
                ->orWhere('name', 'accounts_groups')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
        ]);
        SubpanelField::insert([
            'subpanel_id' => $id,
            'field_id' => Field::where('module_id',
                Module::where('name', 'groups')->first()->id
            )->first()->id
        ]);


        Log::info("ModuleSubpanel Seeding Complete");
    }
}

