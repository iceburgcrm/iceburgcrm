<?php

namespace Database\Seeders;

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
        ModuleGroup::truncate();
        Module::truncate();

        $this->seedModuleGroups();
        $this->seedModules();
        $this->seedConvertedModules();
    }

    private function seedConvertedModules()
    {
        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'leads')->first()->id,
            'module_id' => Module::where('name', 'contacts')->first()->id,
            'level' => 1,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'contacts')->first()->id,
            'module_id' => Module::where('name', 'accounts')->first()->id,
            'level' => 2,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'accounts')->first()->id,
            'module_id' => Module::where('name', 'quotes')->first()->id,
            'level' => 3,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'quotes')->first()->id,
            'module_id' => Module::where('name', 'opportunities')->first()->id,
            'level' => 4,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'opportunities')->first()->id,
            'module_id' => Module::where('name', 'contracts')->first()->id,
            'level' => 5,
        ]);

        ModuleConvertable::insert([
            'primary_module_id' => Module::where('name', 'contracts')->first()->id,
            'module_id' => 0,
            'level' => 6,
        ]);

    }

    private function seedModuleGroups()
    {
        ModuleGroup::insert([
            'id' => '1',
            'name' => 'companies',
            'label' => 'Companies',
            'view_order' => 0,
        ]);

        ModuleGroup::insert([
            'id' => '2',
            'name' => 'marketing',
            'label' => 'Marketing',
            'view_order' => 0,
        ]);
        ModuleGroup::insert([
            'id' => '3',
            'name' => 'sales',
            'label' => 'Sales',
            'view_order' => 0,
        ]);

        ModuleGroup::insert([
            'id' => '4',
            'name' => 'communications',
            'label' => 'Communications',
            'view_order' => 0,
        ]);
        ModuleGroup::insert([
            'id' => '5',
            'name' => 'more',
            'label' => 'More',
            'view_order' => 0,
        ]);

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
            'name' => 'accounts',
            'label' => 'Accounts',
            'description' => 'Account module',
            'view_order' => $order++,
            'module_group_id' => 1,
            'icon' => 'BuildingOffice2Icon',
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'contacts',
            'label' => 'Contacts',
            'description' => 'Contact module',
            'view_order' => $order++,
            'module_group_id' => 1,
            'icon' => 'UserIcon',
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'contracts',
            'label' => 'Contracts',
            'description' => 'Contract module',
            'view_order' => $order++,
            'module_group_id' => 1,
            'icon' => 'BookOpenIcon',
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'lineitems',
            'label' => 'Line Items',
            'description' => 'Line Items',
            'view_order' => $order++,
            'module_group_id' => 3,
            'icon' => 'Bars4Icon',
            'status' => 1,
            'primary' => 1,
        ]);

        $leadId = Module::insertGetId([
            'name' => 'leads',
            'label' => 'Leads',
            'description' => 'Lead module',
            'view_order' => $order++,
            'module_group_id' => 2,
            'icon' => 'UsersIcon',
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'opportunities',
            'label' => 'Opportunities',
            'description' => 'Opportunity module',
            'view_order' => $order++,
            'module_group_id' => 2,
            'icon' => 'LightBulbIcon',
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'meetings',
            'label' => 'Meetings',
            'description' => 'Meetings module',
            'view_order' => $order++,
            'module_group_id' => 4,
            'icon' => 'MegaphoneIcon',
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'cases',
            'label' => 'Cases',
            'description' => 'Cases module',
            'view_order' => $order++,
            'module_group_id' => 2,
            'icon' => 'InboxStackIcon',
            'status' => 1,
            'primary' => 1,
        ]);

/*
        $salesId= Module::insertGetId([
            'name' => 'sales',
            'label' => 'Sales',
            'description' => 'Sales module',
            'view_order' => $order++,
            'module_group_id' => 3,
            'icon' => 'CurrencyDollarIcon',
            'status' => 1,
            'primary' => 1,
        ]);
*/

        Module::insert([
            'name' => 'campaigns',
            'label' => 'Campaigns',
            'description' => 'Campaign module',
            'module_group_id' => 2,
            'view_order' => $order++,
            'icon' => 'ArrowRightOnRectangleIcon',
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'invoices',
            'label' => 'Invoices',
            'description' => 'Invoices module',
            'module_group_id' => 3,
            'icon' => 'CalculatorIcon',
            'view_order' => $order++,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'quotes',
            'label' => 'Quotes',
            'description' => 'Quotes module',
            'icon' => 'QueueListIcon',
            'module_group_id' => 3,
            'view_order' => $order++,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'orders',
            'label' => 'Orders',
            'description' => 'Orders module',
            'module_group_id' => 3,
            'icon' => 'PencilSquareIcon',
            'view_order' => $order++,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'documents',
            'label' => 'Documents',
            'description' => 'Documents module',
            'module_group_id' => 5,
            'icon' => 'DocumentIcon',
            'view_order' => $order++,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'document_types',
            'label' => 'Document Types',
            'description' => 'Document Types module',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'document_status',
            'label' => 'Document Status',
            'description' => 'Documents status module',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'notes',
            'label' => 'Notes',
            'description' => 'Notes module',
            'icon' => 'PencilIcon',
            'module_group_id' => 5,
            'view_order' => $order++,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'groups',
            'label' => 'Groups',
            'description' => 'Groups module',
            'icon' => 'UserGroupIcon',
            'module_group_id' => 5,
            'view_order' => $order++,
            'status' => 1,
            'primary' => 1,
        ]);

        Module::insert([
            'name' => 'projects',
            'label' => 'Projects',
            'description' => 'Project module',
            'icon' => 'RectangleGroupIcon',
            'module_group_id' => 5,
            'view_order' => $order++,
            'status' => 1,
            'primary' => 1,
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

        Module::insert([
            'name' => 'account_status',
            'label' => 'Account Status',
            'description' => 'Account Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1,
        ]);

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
            'name' => 'contract_status',
            'label' => 'Contract Status',
            'description' => 'Contract Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'ice_roles',
            'label' => 'Roles',
            'description' => 'Roles',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'ice_themes',
            'label' => 'Themes',
            'description' => 'Themes',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'status' => 1,
            'create_table' => 1,
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
        ]);

        Module::insert([
            'name' => 'contract_types',
            'label' => 'Contract Types',
            'description' => 'Contract Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'discount_types',
            'label' => 'Discount Types',
            'description' => 'Discount Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'opportunity_types',
            'label' => 'Opportunity Types',
            'description' => 'Opportunity Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'opportunity_status',
            'label' => 'Opportunity Status',
            'description' => 'Opportunity Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'lead_types',
            'label' => 'Lead Types',
            'description' => 'Lead Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'lead_sources',
            'label' => 'Lead Sources',
            'description' => 'Lead Sources',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'lead_status',
            'label' => 'Lead Status',
            'description' => 'Lead Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'meeting_status',
            'label' => 'Meeting Status',
            'description' => 'Meeting Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'meeting_types',
            'label' => 'Meeting Types',
            'description' => 'Meeting Types',
            'view_order' => $order++,
            'icon' => 'PhoneIcon',
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'campaign_status',
            'label' => 'Campaign Status',
            'description' => 'Campaign Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'campaign_types',
            'label' => 'Campaign Types',
            'description' => 'Campaign Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'tasks',
            'label' => 'Tasks',
            'description' => 'Tasks',
            'icon' => 'SparklesIcon',
            'module_group_id' => 6,
            'view_order' => $order++,
            'status' => 1,
            'faker_seed' => 1,
            'create_table' => 1,
        ]);

        Module::insert([
            'name' => 'task_status',
            'label' => 'Task Status',
            'description' => 'Task Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'task_types',
            'label' => 'Task Types',
            'description' => 'Task Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'case_status',
            'label' => 'Case Status',
            'description' => 'Case Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'case_priorities',
            'label' => 'Case Priorities',
            'description' => 'Case Priorities',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'project_priorities',
            'label' => 'Project Priorities',
            'description' => 'Project Priorities',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'case_types',
            'label' => 'Case Types',
            'description' => 'Case Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'project_status',
            'label' => 'Project Status',
            'description' => 'Project Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'project_types',
            'label' => 'Project Types',
            'description' => 'Project Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'quote_status',
            'label' => 'Quote Status',
            'description' => 'Quote Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'invoice_status',
            'label' => 'Invoice Status',
            'description' => 'Invoice Status',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'task_priorities',
            'label' => 'Task Priorities',
            'description' => 'Task Priorities',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'group_types',
            'label' => 'Group Types',
            'description' => 'Group Types',
            'view_order' => $order++,
            'module_group_id' => 6,
            'faker_seed' => 0,
            'create_table' => 1,
            'status' => 1,
        ]);

        Module::insert([
            'name' => 'products',
            'label' => 'Products',
            'description' => 'Products module',
            'module_group_id' => 5,
            'view_order' => $order++,
            'status' => 1,
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
        ]);

        Log::info('Modules Seeding Complete');
    }
}
