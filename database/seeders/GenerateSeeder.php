<?php

namespace Database\Seeders;

use App\Models\Connector;
use App\Models\Datalet;
use App\Models\DataletType;
use App\Models\ConnectorCommand;
use App\Models\Module;
use App\Models\Permission;
use App\Models\Setting;
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
        $this->addSettings();

        Log::info('Generating connectors');
        //$this->addConnectors();

        Log::info('Add Workflow Actions');
       // $this->addWorkflowActions();

        Log::info('Add Datalet Types');
        //$this->addDataletTypes();
        //$this->addDatalets();

        Log::info('Generating module');
        $module = new Module;
        $module->generate($seedAmount);

        Log::info('Generating static lists');

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
            });

        $this->addModulesAndRoles();
        //$this->sampleMedia();
    }

    private function sampleMedia()
    {
        $file = file_get_contents('http://demo.iceburg.ca/seed/video/christmasornaments.mp4');
        if ($file) {
            //  'creative' => 'data:video/mp4;base64,'.base64_encode($file),
            DB::table('campaigns')->insert(
                ['name' => 'Christmas Ad Campaign',
                    'description' => 'Christmas Ad Campaign',
                    'budget' => '10000',
                    'forecast' => '8000',
                    'impressions' => 15346,
                    'currency' => 1,
                    'creative' => 'data:video/mp4;base64,'.base64_encode($file),
                    'campaign_type' => 1,
                    'assigned_to' => 1,
                    'ice_slug' => 'dsfsdfsdfs',
                    'status' => 4,
                    'soft_delete' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );
        }

        $data['created_at'] = date('Y-m-d H:i:s', strtotime('-'.rand(1, 31).' DAY'));
        $data['updated_at'] = $data['created_at'];

        $file = file_get_contents('http://demo.iceburg.ca/seed/recording/sample.ogg');
        //           'audio_recording' => 'data:audio/ogg;base64,'.base64_encode($file),
        if ($file) {
            DB::table('meetings')->insert(
                ['name' => 'Client Meeting',
                    'description' => 'This is a typical service call',
                    'start_date' => strtotime('NOW'),
                    'end_date' => strtotime('NOW'),
                    'location' => 'Office',
                    'audio_recording' => 'data:audio/ogg;base64,'.base64_encode($file),
                    'types' => 1,
                    'assigned_to' => 1,
                    'ice_slug' => 'dsfsdfsdfss',
                    'status' => 4,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]
            );
        }

        Log::info('Finished Seeding sample media');
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
            ['id' => 1, 'name' => 'Doughnut Chart'],
            ['id' => 2, 'name' => 'Line Chart'],
            ['id' => 3, 'name' => 'Bar Graph'],
            ['id' => 4, 'name' => 'Pie Chart'],
            ['id' => 5, 'name' => 'Area Chart'],
            ['id' => 6, 'name' => 'Latest Meetings'],
            ['id' => 7, 'name' => 'CRM Stats'],
            ['id' => 8, 'name' => 'Totals Report'],
        ]);
    }

    private function addDataLets()
    {
        Datalet::truncate();
        Datalet::insert([

            ['type' => 1,
                'module_id' => 0,
                'label' => 'Total Sales',
                'size' => 12,
                'display_order' => 1],
            ['type' => 2,
                'module_id' => 0,
                'label' => 'Number of new Leads / Contacts / Accounts over the last 7 Days',
                'size' => 12,
                'display_order' => 2],
            ['type' => 3,
                'module_id' => 0,
                'label' => 'Meetings',
                'size' => 12,
                'display_order' => 4],
            ['type' => 4,
                'module_id' => 0,
                'label' => 'Number of new Opportunities / Quotes / Contracts over the last 7 Days',
                'size' => 12,
                'display_order' => 5],
            ['type' => 1,
                'module_id' => 0,
                'label' => 'Orders This Month',
                'size' => 12,
                'display_order' => 7],
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
            'value' => 'light',
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

    }

    private function addConnectors()
    {
        /*
        $connectorId = Connector::insertGetId([
            'name' => 'joke of the day',
            'base_url' => 'https://official-joke-api.appspot.com',
        ]);

        ConnectorCommand::insert([
            'connector_id' => $connectorId,
            'endpoint' => '/random_joke',
            'class_name' => 'jokes',
        ]);
        */
    }

    private function Account_Status()
    {
        return [
            ['id' => 1, 'name' => 'Prospect'],
            ['id' => 2, 'name' => 'Sold'],
            ['id' => 3, 'name' => 'Active'],
            ['id' => 4, 'name' => 'Inactive'],
            ['id' => 5, 'name' => 'Cancelled'],
            ['id' => 6, 'name' => 'Closed'],
        ];
    }

    private function Contract_Status()
    {
        return [
            ['id' => 1, 'name' => 'Active'],
            ['id' => 2, 'name' => 'Inactive'],
            ['id' => 3, 'name' => 'Pending'],
            ['id' => 4, 'name' => 'Cancelled'],
            ['id' => 5, 'name' => 'Suspended'],
            ['id' => 6, 'name' => 'Terminated'],
            ['id' => 7, 'name' => 'Deleted'],
        ];
    }

    private function Project_Status()
    {
        return [
            ['id' => 1, 'name' => 'Active'],
            ['id' => 2, 'name' => 'Inactive'],
            ['id' => 3, 'name' => 'Pending'],
            ['id' => 4, 'name' => 'Cancelled'],
            ['id' => 5, 'name' => 'Suspended'],
            ['id' => 6, 'name' => 'Terminated'],
            ['id' => 7, 'name' => 'Deleted'],
        ];
    }

    private function Ice_Roles()
    {
        return [
            ['id' => 1, 'name' => 'Admin'],
            ['id' => 2, 'name' => 'User'],
            ['id' => 3, 'name' => 'Sales'],
            ['id' => 4, 'name' => 'Accounting'],
            ['id' => 5, 'name' => 'Support'],
            ['id' => 6, 'name' => 'Marketing'],
            ['id' => 7, 'name' => 'HR'],
        ];
    }

    private function Currency()
    {
        return [
            ['code' => 'AFN', 'name' => 'Afghani', 'symbol' => '؋'],
            ['code' => 'ALL', 'name' => 'Lek', 'symbol' => 'Lek'],
            ['code' => 'ANG', 'name' => 'Netherlands Antillian Guilder', 'symbol' => 'ƒ'],
            ['code' => 'ARS', 'name' => 'Argentine Peso', 'symbol' => '$'],
            ['code' => 'AUD', 'name' => 'Australian Dollar', 'symbol' => '$'],
            ['code' => 'AWG', 'name' => 'Aruban Guilder', 'symbol' => 'ƒ'],
            ['code' => 'AZN', 'name' => 'Azerbaijanian Manat', 'symbol' => 'ман'],
            ['code' => 'BAM', 'name' => 'Convertible Marks', 'symbol' => 'KM'],
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳'],
            ['code' => 'BBD', 'name' => 'Barbados Dollar', 'symbol' => '$'],
            ['code' => 'BGN', 'name' => 'Bulgarian Lev', 'symbol' => 'лв'],
            ['code' => 'BMD', 'name' => 'Bermudian Dollar', 'symbol' => '$'],
            ['code' => 'BND', 'name' => 'Brunei Dollar', 'symbol' => '$'],
            ['code' => 'BOB', 'name' => 'BOV Boliviano Mvdol', 'symbol' => '$b'],
            ['code' => 'BRL', 'name' => 'Brazilian Real', 'symbol' => 'R$'],
            ['code' => 'BSD', 'name' => 'Bahamian Dollar', 'symbol' => '$'],
            ['code' => 'BWP', 'name' => 'Pula', 'symbol' => 'P'],
            ['code' => 'BYR', 'name' => 'Belarussian Ruble', 'symbol' => '₽'],
            ['code' => 'BZD', 'name' => 'Belize Dollar', 'symbol' => 'BZ$'],
            ['code' => 'CAD', 'name' => 'Canadian Dollar', 'symbol' => '$'],
            ['code' => 'CHF', 'name' => 'Swiss Franc', 'symbol' => 'CHF'],
            ['code' => 'CLP', 'name' => 'CLF Chilean Peso Unidades de fomento', 'symbol' => '$'],
            ['code' => 'CNY', 'name' => 'Yuan Renminbi', 'symbol' => '¥'],
            ['code' => 'COP', 'name' => 'COU Colombian Peso Unidad de Valor Real', 'symbol' => '$'],
            ['code' => 'CRC', 'name' => 'Costa Rican Colon', 'symbol' => '₡'],
            ['code' => 'CUP', 'name' => 'CUC Cuban Peso Peso Convertible', 'symbol' => '₱'],
            ['code' => 'CZK', 'name' => 'Czech Koruna', 'symbol' => 'Kč'],
            ['code' => 'DKK', 'name' => 'Danish Krone', 'symbol' => 'kr'],
            ['code' => 'DOP', 'name' => 'Dominican Peso', 'symbol' => 'RD$'],
            ['code' => 'EGP', 'name' => 'Egyptian Pound', 'symbol' => '£'],
            ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€'],
            ['code' => 'FJD', 'name' => 'Fiji Dollar', 'symbol' => '$'],
            ['code' => 'FKP', 'name' => 'Falkland Islands Pound', 'symbol' => '£'],
            ['code' => 'GBP', 'name' => 'Pound Sterling', 'symbol' => '£'],
            ['code' => 'GIP', 'name' => 'Gibraltar Pound', 'symbol' => '£'],
            ['code' => 'GTQ', 'name' => 'Quetzal', 'symbol' => 'Q'],
            ['code' => 'GYD', 'name' => 'Guyana Dollar', 'symbol' => '$'],
            ['code' => 'HKD', 'name' => 'Hong Kong Dollar', 'symbol' => '$'],
            ['code' => 'HNL', 'name' => 'Lempira', 'symbol' => 'L'],
            ['code' => 'HRK', 'name' => 'Croatian Kuna', 'symbol' => 'kn'],
            ['code' => 'HUF', 'name' => 'Forint', 'symbol' => 'Ft'],
            ['code' => 'IDR', 'name' => 'Rupiah', 'symbol' => 'Rp'],
            ['code' => 'ILS', 'name' => 'New Israeli Sheqel', 'symbol' => '₪'],
            ['code' => 'IRR', 'name' => 'Iranian Rial', 'symbol' => '﷼'],
            ['code' => 'ISK', 'name' => 'Iceland Krona', 'symbol' => 'kr'],
            ['code' => 'JMD', 'name' => 'Jamaican Dollar', 'symbol' => 'J$'],
            ['code' => 'JPY', 'name' => 'Yen', 'symbol' => '¥'],
            ['code' => 'KGS', 'name' => 'Som', 'symbol' => 'лв'],
            ['code' => 'KHR', 'name' => 'Riel', 'symbol' => '៛'],
            ['code' => 'KPW', 'name' => 'North Korean Won', 'symbol' => '₩'],
            ['code' => 'KRW', 'name' => 'Won', 'symbol' => '₩'],
            ['code' => 'KYD', 'name' => 'Cayman Islands Dollar', 'symbol' => '$'],
            ['code' => 'KZT', 'name' => 'Tenge', 'symbol' => 'лв'],
            ['code' => 'LAK', 'name' => 'Kip', 'symbol' => '₭'],
            ['code' => 'LBP', 'name' => 'Lebanese Pound', 'symbol' => '£'],
            ['code' => 'LKR', 'name' => 'Sri Lanka Rupee', 'symbol' => '₨'],
            ['code' => 'LRD', 'name' => 'Liberian Dollar', 'symbol' => '$'],
            ['code' => 'LTL', 'name' => 'Lithuanian Litas', 'symbol' => 'Lt'],
            ['code' => 'LVL', 'name' => 'Latvian Lats', 'symbol' => 'Ls'],
            ['code' => 'MKD', 'name' => 'Denar', 'symbol' => 'ден'],
            ['code' => 'MNT', 'name' => 'Tugrik', 'symbol' => '₮'],
            ['code' => 'MUR', 'name' => 'Mauritius Rupee', 'symbol' => '₨'],
            ['code' => 'MXN', 'name' => 'MXV Mexican Peso Mexican Unidad de Inversion (UDI]', 'symbol' => '$'],
            ['code' => 'MYR', 'name' => 'Malaysian Ringgit', 'symbol' => 'RM'],
            ['code' => 'MZN', 'name' => 'Metical', 'symbol' => 'MT'],
            ['code' => 'NGN', 'name' => 'Naira', 'symbol' => '₦'],
            ['code' => 'NIO', 'name' => 'Cordoba Oro', 'symbol' => 'C$'],
            ['code' => 'NOK', 'name' => 'Norwegian Krone', 'symbol' => 'kr'],
            ['code' => 'NPR', 'name' => 'Nepalese Rupee', 'symbol' => '₨'],
            ['code' => 'NZD', 'name' => 'New Zealand Dollar', 'symbol' => '$'],
            ['code' => 'OMR', 'name' => 'Rial Omani', 'symbol' => '﷼'],
            ['code' => 'PAB', 'name' => 'USD Balboa US Dollar', 'symbol' => 'B/.'],
            ['code' => 'PEN', 'name' => 'Nuevo Sol', 'symbol' => 'S/.'],
            ['code' => 'PHP', 'name' => 'Philippine Peso', 'symbol' => 'Php'],
            ['code' => 'PKR', 'name' => 'Pakistan Rupee', 'symbol' => '₨'],
            ['code' => 'PLN', 'name' => 'Zloty', 'symbol' => 'zł'],
            ['code' => 'PYG', 'name' => 'Guarani', 'symbol' => 'Gs'],
            ['code' => 'QAR', 'name' => 'Qatari Rial', 'symbol' => '﷼'],
            ['code' => 'RON', 'name' => 'New Leu', 'symbol' => 'lei'],
            ['code' => 'RSD', 'name' => 'Serbian Dinar', 'symbol' => 'Дин.'],
            ['code' => 'RUB', 'name' => 'Russian Ruble', 'symbol' => 'руб'],
            ['code' => 'SAR', 'name' => 'Saudi Riyal', 'symbol' => '﷼'],
            ['code' => 'SBD', 'name' => 'Solomon Islands Dollar', 'symbol' => '$'],
            ['code' => 'SCR', 'name' => 'Seychelles Rupee', 'symbol' => '₨'],
            ['code' => 'SEK', 'name' => 'Swedish Krona', 'symbol' => 'kr'],
            ['code' => 'SGD', 'name' => 'Singapore Dollar', 'symbol' => '$'],
            ['code' => 'SHP', 'name' => 'Saint Helena Pound', 'symbol' => '£'],
            ['code' => 'SOS', 'name' => 'Somali Shilling', 'symbol' => 'S'],
            ['code' => 'SRD', 'name' => 'Surinam Dollar', 'symbol' => '$'],
            ['code' => 'SVC', 'name' => 'USD El Salvador Colon US Dollar', 'symbol' => '$'],
            ['code' => 'SYP', 'name' => 'Syrian Pound', 'symbol' => '£'],
            ['code' => 'THB', 'name' => 'Baht', 'symbol' => '฿'],
            ['code' => 'TRY', 'name' => 'Turkish Lira', 'symbol' => 'TL'],
            ['code' => 'TTD', 'name' => 'Trinidad and Tobago Dollar', 'symbol' => 'TT$'],
            ['code' => 'TWD', 'name' => 'New Taiwan Dollar', 'symbol' => 'NT$'],
            ['code' => 'UAH', 'name' => 'Hryvnia', 'symbol' => '₴'],
            ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$'],
            ['code' => 'UYU', 'name' => 'UYI Uruguay Peso en Unidades Indexadas', 'symbol' => '$U'],
            ['code' => 'UZS', 'name' => 'Uzbekistan Sum', 'symbol' => 'лв'],
            ['code' => 'VEF', 'name' => 'Bolivar Fuerte', 'symbol' => 'Bs'],
            ['code' => 'VND', 'name' => 'Dong', 'symbol' => '₫'],
            ['code' => 'XCD', 'name' => 'East Caribbean Dollar', 'symbol' => '$'],
            ['code' => 'YER', 'name' => 'Yemeni Rial', 'symbol' => '﷼'],
            ['code' => 'ZAR', 'name' => 'Rand', 'symbol' => 'R'],
        ];
    }

    private function Ice_Themes()
    {
        return [
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
        ];
    }

    public function Contract_Types()
    {
        return [
            ['id' => 1, 'name' => 'Sale'],
            ['id' => 2, 'name' => 'Rental'],
            ['id' => 3, 'name' => 'Lease'],
            ['id' => 4, 'name' => 'Purchase'],
            ['id' => 5, 'name' => 'Other'],
        ];
    }

    public function Document_Types()
    {
        return [
            ['id' => 1, 'name' => 'Text'],
            ['id' => 2, 'name' => 'Word'],
            ['id' => 3, 'name' => 'PDF'],
            ['id' => 4, 'name' => 'Open Office'],
            ['id' => 5, 'name' => 'Other'],
        ];
    }

    public function Project_Types()
    {
        return [
            ['id' => 1, 'name' => 'Feature'],
            ['id' => 2, 'name' => 'Bug'],
            ['id' => 3, 'name' => 'Other'],
        ];
    }

    public function Contract_Terms()
    {
        return [
            ['id' => 1, 'name' => 'Monthly'],
            ['id' => 2, 'name' => 'Quarterly'],
            ['id' => 3, 'name' => 'Semi-Annually'],
            ['id' => 4, 'name' => 'Annually'],
            ['id' => 5, 'name' => 'Other'],
        ];
    }

    public function Contract_Payment_Terms()
    {
        return [
            ['id' => 1, 'name' => 'Cash'],
            ['id' => 2, 'name' => 'Check'],
            ['id' => 3, 'name' => 'Credit Card'],
            ['id' => 4, 'name' => 'Other'],
        ];
    }

    public function Lead_Types()
    {
        return [
            ['id' => 1, 'name' => 'Prospect'],
            ['id' => 2, 'name' => 'Customer'],
            ['id' => 3, 'name' => 'Other'],
            ['id' => 4, 'name' => 'Converted'],
        ];
    }

    public function Lead_Status()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Assigned'],
            ['id' => 3, 'name' => 'In Progress'],
            ['id' => 4, 'name' => 'Converted'],
            ['id' => 5, 'name' => 'Closed'],
            ['id' => 6, 'name' => 'Deleted'],
        ];
    }

    public function Document_Status()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Draft'],
            ['id' => 3, 'name' => 'In Progress'],
            ['id' => 4, 'name' => 'Active'],
            ['id' => 5, 'name' => 'Closed'],
            ['id' => 6, 'name' => 'Deleted'],
        ];
    }

    public function Quote_Status()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Assigned'],
            ['id' => 3, 'name' => 'In Progress'],
            ['id' => 4, 'name' => 'Converted'],
            ['id' => 5, 'name' => 'Closed'],
            ['id' => 6, 'name' => 'Deleted'],
        ];
    }

    public function Lead_Sources()
    {
        return [
            ['id' => 1, 'name' => 'Cold Call'],
            ['id' => 2, 'name' => 'Existing Customer'],
            ['id' => 3, 'name' => 'Self Generated'],
            ['id' => 4, 'name' => 'Employee'],
            ['id' => 5, 'name' => 'Partner'],
            ['id' => 6, 'name' => 'Public Relations'],
            ['id' => 7, 'name' => 'Direct Mail'],
            ['id' => 8, 'name' => 'Conference'],
            ['id' => 9, 'name' => 'Trade Show'],
            ['id' => 10, 'name' => 'Web Site'],
            ['id' => 11, 'name' => 'Word of Mouth'],
            ['id' => 12, 'name' => 'Email'],
            ['id' => 13, 'name' => 'Campaign'],
            ['id' => 14, 'name' => 'Other'],
        ];
    }

    public function Lead_Priorities()
    {
        return [
            ['id' => 1, 'name' => 'Low'],
            ['id' => 2, 'name' => 'Medium'],
            ['id' => 3, 'name' => 'High'],
            ['id' => 4, 'name' => 'Urgent'],
        ];
    }

    public function Task_Priorities()
    {
        return [
            ['id' => 1, 'name' => 'Low'],
            ['id' => 2, 'name' => 'Medium'],
            ['id' => 3, 'name' => 'High'],
            ['id' => 4, 'name' => 'Urgent'],
        ];
    }

    public function Group_Types()
    {
        return [
            ['id' => 1, 'name' => 'Sales'],
            ['id' => 2, 'name' => 'Marketing'],
            ['id' => 3, 'name' => 'Admin'],
        ];
    }

    public function Opportunity_Types()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Existing'],
            ['id' => 3, 'name' => 'Other'],
        ];
    }

    public function Opportunity_Status()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Assigned'],
            ['id' => 3, 'name' => 'In Progress'],
            ['id' => 4, 'name' => 'Closed'],
            ['id' => 5, 'name' => 'Deleted'],
        ];
    }

    public function Campaign_Status()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Assigned'],
            ['id' => 3, 'name' => 'In Progress'],
            ['id' => 4, 'name' => 'Closed'],
            ['id' => 5, 'name' => 'Deleted'],
        ];
    }

    public function Case_Status()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Assigned'],
            ['id' => 3, 'name' => 'In Progress'],
            ['id' => 4, 'name' => 'Closed'],
            ['id' => 5, 'name' => 'Deleted'],
        ];
    }

    public function Task_Status()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Assigned'],
            ['id' => 3, 'name' => 'In Progress'],
            ['id' => 4, 'name' => 'Closed'],
            ['id' => 5, 'name' => 'Deleted'],
        ];
    }

    public function Task_Types()
    {
        return [
            ['id' => 1, 'name' => 'User'],
            ['id' => 2, 'name' => 'System'],
        ];
    }

    public function Campaign_Types()
    {
        return [
            ['id' => 1, 'name' => 'Email'],
            ['id' => 2, 'name' => 'Facebook'],
            ['id' => 3, 'name' => 'Adwords'],
            ['id' => 4, 'name' => 'Web'],
            ['id' => 5, 'name' => 'Mail'],
            ['id' => 6, 'name' => 'Print'],
            ['id' => 7, 'name' => 'Other'],
        ];
    }

    public function Opportunity_Priorities()
    {
        return [
            ['id' => 1, 'name' => 'Low'],
            ['id' => 2, 'name' => 'Medium'],
            ['id' => 3, 'name' => 'High'],
            ['id' => 4, 'name' => 'Urgent'],
        ];
    }

    public function Project_Priorities()
    {
        return [
            ['id' => 1, 'name' => 'Low'],
            ['id' => 2, 'name' => 'Medium'],
            ['id' => 3, 'name' => 'High'],
            ['id' => 4, 'name' => 'Urgent'],
        ];
    }

    public function Case_Priorities()
    {
        return [
            ['id' => 1, 'name' => 'Low'],
            ['id' => 2, 'name' => 'Medium'],
            ['id' => 3, 'name' => 'High'],
            ['id' => 4, 'name' => 'Urgent'],
        ];
    }

    public function Invoice_Status()
    {
        return [
            ['id' => 1, 'name' => 'New'],
            ['id' => 2, 'name' => 'Paid'],
            ['id' => 3, 'name' => 'Partially Paid'],
            ['id' => 4, 'name' => 'Overdue'],
            ['id' => 5, 'name' => 'Cancelled'],
        ];
    }

    public function Meeting_Status()
    {
        return [
            ['id' => 1, 'name' => 'Planned'],
            ['id' => 2, 'name' => 'Held'],
            ['id' => 3, 'name' => 'Cancelled'],
        ];
    }

    public function Meeting_Types()
    {
        return [
            ['id' => 1, 'name' => 'General'],
            ['id' => 2, 'name' => 'Sales'],
            ['id' => 3, 'name' => 'Support'],
            ['id' => 4, 'name' => 'Other'],
        ];
    }

    public function Case_Types()
    {
        return [
            ['id' => 1, 'name' => 'General'],
            ['id' => 2, 'name' => 'Sales'],
            ['id' => 3, 'name' => 'Support'],
            ['id' => 4, 'name' => 'Other'],
        ];
    }

    public function Discount_Types()
    {
        return [
            ['id' => 1, 'name' => 'Percentage'],
            ['id' => 2, 'name' => 'Amount'],
        ];
    }

    public function Countries()
    {
        return [
            ['code' => 'US', 'name' => 'Canada'],
            ['code' => 'AF', 'name' => 'Afghanistan'],
            ['code' => 'AL', 'name' => 'Albania'],
            ['code' => 'DZ', 'name' => 'Algeria'],
            ['code' => 'AS', 'name' => 'American Samoa'],
            ['code' => 'AD', 'name' => 'Andorra'],
            ['code' => 'AO', 'name' => 'Angola'],
            ['code' => 'AI', 'name' => 'Anguilla'],
            ['code' => 'AQ', 'name' => 'Antarctica'],
            ['code' => 'AG', 'name' => 'Antigua and/or Barbuda'],
            ['code' => 'AR', 'name' => 'Argentina'],
            ['code' => 'AM', 'name' => 'Armenia'],
            ['code' => 'AW', 'name' => 'Aruba'],
            ['code' => 'AU', 'name' => 'Australia'],
            ['code' => 'AT', 'name' => 'Austria'],
            ['code' => 'AZ', 'name' => 'Azerbaijan'],
            ['code' => 'BS', 'name' => 'Bahamas'],
            ['code' => 'BH', 'name' => 'Bahrain'],
            ['code' => 'BD', 'name' => 'Bangladesh'],
            ['code' => 'BB', 'name' => 'Barbados'],
            ['code' => 'BY', 'name' => 'Belarus'],
            ['code' => 'BE', 'name' => 'Belgium'],
            ['code' => 'BZ', 'name' => 'Belize'],
            ['code' => 'BJ', 'name' => 'Benin'],
            ['code' => 'BM', 'name' => 'Bermuda'],
            ['code' => 'BT', 'name' => 'Bhutan'],
            ['code' => 'BO', 'name' => 'Bolivia'],
            ['code' => 'BA', 'name' => 'Bosnia and Herzegovina'],
            ['code' => 'BW', 'name' => 'Botswana'],
            ['code' => 'BV', 'name' => 'Bouvet Island'],
            ['code' => 'BR', 'name' => 'Brazil'],
            ['code' => 'IO', 'name' => 'British lndian Ocean Territory'],
            ['code' => 'BN', 'name' => 'Brunei Darussalam'],
            ['code' => 'BG', 'name' => 'Bulgaria'],
            ['code' => 'BF', 'name' => 'Burkina Faso'],
            ['code' => 'BI', 'name' => 'Burundi'],
            ['code' => 'KH', 'name' => 'Cambodia'],
            ['code' => 'CM', 'name' => 'Cameroon'],
            ['code' => 'CV', 'name' => 'Cape Verde'],
            ['code' => 'KY', 'name' => 'Cayman Islands'],
            ['code' => 'CF', 'name' => 'Central African Republic'],
            ['code' => 'TD', 'name' => 'Chad'],
            ['code' => 'CL', 'name' => 'Chile'],
            ['code' => 'CN', 'name' => 'China'],
            ['code' => 'CX', 'name' => 'Christmas Island'],
            ['code' => 'CC', 'name' => 'Cocos (Keeling) Islands'],
            ['code' => 'CO', 'name' => 'Colombia'],
            ['code' => 'KM', 'name' => 'Comoros'],
            ['code' => 'CG', 'name' => 'Congo'],
            ['code' => 'CK', 'name' => 'Cook Islands'],
            ['code' => 'CR', 'name' => 'Costa Rica'],
            ['code' => 'HR', 'name' => 'Croatia (Hrvatska)'],
            ['code' => 'CU', 'name' => 'Cuba'],
            ['code' => 'CY', 'name' => 'Cyprus'],
            ['code' => 'CZ', 'name' => 'Czech Republic'],
            ['code' => 'DK', 'name' => 'Denmark'],
            ['code' => 'DJ', 'name' => 'Djibouti'],
            ['code' => 'DM', 'name' => 'Dominica'],
            ['code' => 'DO', 'name' => 'Dominican Republic'],
            ['code' => 'TP', 'name' => 'East Timor'],
            ['code' => 'EC', 'name' => 'Ecudaor'],
            ['code' => 'EG', 'name' => 'Egypt'],
            ['code' => 'SV', 'name' => 'El Salvador'],
            ['code' => 'GQ', 'name' => 'Equatorial Guinea'],
            ['code' => 'ER', 'name' => 'Eritrea'],
            ['code' => 'EE', 'name' => 'Estonia'],
            ['code' => 'ET', 'name' => 'Ethiopia'],
            ['code' => 'FK', 'name' => 'Falkland Islands (Malvinas)'],
            ['code' => 'FO', 'name' => 'Faroe Islands'],
            ['code' => 'FJ', 'name' => 'Fiji'],
            ['code' => 'FI', 'name' => 'Finland'],
            ['code' => 'FR', 'name' => 'France'],
            ['code' => 'FX', 'name' => 'France, Metropolitan'],
            ['code' => 'GF', 'name' => 'French Guiana'],
            ['code' => 'PF', 'name' => 'French Polynesia'],
            ['code' => 'TF', 'name' => 'French Southern Territories'],
            ['code' => 'GA', 'name' => 'Gabon'],
            ['code' => 'GM', 'name' => 'Gambia'],
            ['code' => 'GE', 'name' => 'Georgia'],
            ['code' => 'DE', 'name' => 'Germany'],
            ['code' => 'GH', 'name' => 'Ghana'],
            ['code' => 'GI', 'name' => 'Gibraltar'],
            ['code' => 'GR', 'name' => 'Greece'],
            ['code' => 'GL', 'name' => 'Greenland'],
            ['code' => 'GD', 'name' => 'Grenada'],
            ['code' => 'GP', 'name' => 'Guadeloupe'],
            ['code' => 'GU', 'name' => 'Guam'],
            ['code' => 'GT', 'name' => 'Guatemala'],
            ['code' => 'GN', 'name' => 'Guinea'],
            ['code' => 'GW', 'name' => 'Guinea-Bissau'],
            ['code' => 'GY', 'name' => 'Guyana'],
            ['code' => 'HT', 'name' => 'Haiti'],
            ['code' => 'HM', 'name' => 'Heard and Mc Donald Islands'],
            ['code' => 'HN', 'name' => 'Honduras'],
            ['code' => 'HK', 'name' => 'Hong Kong'],
            ['code' => 'HU', 'name' => 'Hungary'],
            ['code' => 'IS', 'name' => 'Iceland'],
            ['code' => 'IN', 'name' => 'India'],
            ['code' => 'ID', 'name' => 'Indonesia'],
            ['code' => 'IR', 'name' => 'Iran (Islamic Republic of)'],
            ['code' => 'IQ', 'name' => 'Iraq'],
            ['code' => 'IE', 'name' => 'Ireland'],
            ['code' => 'IL', 'name' => 'Israel'],
            ['code' => 'IT', 'name' => 'Italy'],
            ['code' => 'CI', 'name' => 'Ivory Coast'],
            ['code' => 'JM', 'name' => 'Jamaica'],
            ['code' => 'JP', 'name' => 'Japan'],
            ['code' => 'JO', 'name' => 'Jordan'],
            ['code' => 'KZ', 'name' => 'Kazakhstan'],
            ['code' => 'KE', 'name' => 'Kenya'],
            ['code' => 'KI', 'name' => 'Kiribati'],
            ['code' => 'KP', 'name' => 'Korea, Democratic People\'s Republic of'],
            ['code' => 'KR', 'name' => 'Korea, Republic of'],
            ['code' => 'KW', 'name' => 'Kuwait'],
            ['code' => 'KG', 'name' => 'Kyrgyzstan'],
            ['code' => 'LA', 'name' => 'Lao People\'s Democratic Republic'],
            ['code' => 'LV', 'name' => 'Latvia'],
            ['code' => 'LB', 'name' => 'Lebanon'],
            ['code' => 'LS', 'name' => 'Lesotho'],
            ['code' => 'LR', 'name' => 'Liberia'],
            ['code' => 'LY', 'name' => 'Libyan Arab Jamahiriya'],
            ['code' => 'LI', 'name' => 'Liechtenstein'],
            ['code' => 'LT', 'name' => 'Lithuania'],
            ['code' => 'LU', 'name' => 'Luxembourg'],
            ['code' => 'MO', 'name' => 'Macau'],
            ['code' => 'MK', 'name' => 'Macedonia'],
            ['code' => 'MG', 'name' => 'Madagascar'],
            ['code' => 'MW', 'name' => 'Malawi'],
            ['code' => 'MY', 'name' => 'Malaysia'],
            ['code' => 'MV', 'name' => 'Maldives'],
            ['code' => 'ML', 'name' => 'Mali'],
            ['code' => 'MT', 'name' => 'Malta'],
            ['code' => 'MH', 'name' => 'Marshall Islands'],
            ['code' => 'MQ', 'name' => 'Martinique'],
            ['code' => 'MR', 'name' => 'Mauritania'],
            ['code' => 'MU', 'name' => 'Mauritius'],
            ['code' => 'TY', 'name' => 'Mayotte'],
            ['code' => 'MX', 'name' => 'Mexico'],
            ['code' => 'FM', 'name' => 'Micronesia, Federated States of'],
            ['code' => 'MD', 'name' => 'Moldova, Republic of'],
            ['code' => 'MC', 'name' => 'Monaco'],
            ['code' => 'MN', 'name' => 'Mongolia'],
            ['code' => 'MS', 'name' => 'Montserrat'],
            ['code' => 'MA', 'name' => 'Morocco'],
            ['code' => 'MZ', 'name' => 'Mozambique'],
            ['code' => 'MM', 'name' => 'Myanmar'],
            ['code' => 'NA', 'name' => 'Namibia'],
            ['code' => 'NR', 'name' => 'Nauru'],
            ['code' => 'NP', 'name' => 'Nepal'],
            ['code' => 'NL', 'name' => 'Netherlands'],
            ['code' => 'AN', 'name' => 'Netherlands Antilles'],
            ['code' => 'NC', 'name' => 'New Caledonia'],
            ['code' => 'NZ', 'name' => 'New Zealand'],
            ['code' => 'NI', 'name' => 'Nicaragua'],
            ['code' => 'NE', 'name' => 'Niger'],
            ['code' => 'NG', 'name' => 'Nigeria'],
            ['code' => 'NU', 'name' => 'Niue'],
            ['code' => 'NF', 'name' => 'Norfork Island'],
            ['code' => 'MP', 'name' => 'Northern Mariana Islands'],
            ['code' => 'NO', 'name' => 'Norway'],
            ['code' => 'OM', 'name' => 'Oman'],
            ['code' => 'PK', 'name' => 'Pakistan'],
            ['code' => 'PW', 'name' => 'Palau'],
            ['code' => 'PA', 'name' => 'Panama'],
            ['code' => 'PG', 'name' => 'Papua New Guinea'],
            ['code' => 'PY', 'name' => 'Paraguay'],
            ['code' => 'PE', 'name' => 'Peru'],
            ['code' => 'PH', 'name' => 'Philippines'],
            ['code' => 'PN', 'name' => 'Pitcairn'],
            ['code' => 'PL', 'name' => 'Poland'],
            ['code' => 'PT', 'name' => 'Portugal'],
            ['code' => 'PR', 'name' => 'Puerto Rico'],
            ['code' => 'QA', 'name' => 'Qatar'],
            ['code' => 'RE', 'name' => 'Reunion'],
            ['code' => 'RO', 'name' => 'Romania'],
            ['code' => 'RU', 'name' => 'Russian Federation'],
            ['code' => 'RW', 'name' => 'Rwanda'],
            ['code' => 'KN', 'name' => 'Saint Kitts and Nevis'],
            ['code' => 'LC', 'name' => 'Saint Lucia'],
            ['code' => 'VC', 'name' => 'Saint Vincent and the Grenadines'],
            ['code' => 'WS', 'name' => 'Samoa'],
            ['code' => 'SM', 'name' => 'San Marino'],
            ['code' => 'ST', 'name' => 'Sao Tome and Principe'],
            ['code' => 'SA', 'name' => 'Saudi Arabia'],
            ['code' => 'SN', 'name' => 'Senegal'],
            ['code' => 'RS', 'name' => 'Serbia'],
            ['code' => 'SC', 'name' => 'Seychelles'],
            ['code' => 'SL', 'name' => 'Sierra Leone'],
            ['code' => 'SG', 'name' => 'Singapore'],
            ['code' => 'SK', 'name' => 'Slovakia'],
            ['code' => 'SI', 'name' => 'Slovenia'],
            ['code' => 'SB', 'name' => 'Solomon Islands'],
            ['code' => 'SO', 'name' => 'Somalia'],
            ['code' => 'ZA', 'name' => 'South Africa'],
            ['code' => 'GS', 'name' => 'South Georgia South Sandwich Islands'],
            ['code' => 'ES', 'name' => 'Spain'],
            ['code' => 'LK', 'name' => 'Sri Lanka'],
            ['code' => 'SH', 'name' => 'St. Helena'],
            ['code' => 'PM', 'name' => 'St. Pierre and Miquelon'],
            ['code' => 'SD', 'name' => 'Sudan'],
            ['code' => 'SR', 'name' => 'Suriname'],
            ['code' => 'SJ', 'name' => 'Svalbarn and Jan Mayen Islands'],
            ['code' => 'SZ', 'name' => 'Swaziland'],
            ['code' => 'SE', 'name' => 'Sweden'],
            ['code' => 'CH', 'name' => 'Switzerland'],
            ['code' => 'SY', 'name' => 'Syrian Arab Republic'],
            ['code' => 'TW', 'name' => 'Taiwan'],
            ['code' => 'TJ', 'name' => 'Tajikistan'],
            ['code' => 'TZ', 'name' => 'Tanzania, United Republic of'],
            ['code' => 'TH', 'name' => 'Thailand'],
            ['code' => 'TG', 'name' => 'Togo'],
            ['code' => 'TK', 'name' => 'Tokelau'],
            ['code' => 'TO', 'name' => 'Tonga'],
            ['code' => 'TT', 'name' => 'Trinidad and Tobago'],
            ['code' => 'TN', 'name' => 'Tunisia'],
            ['code' => 'TR', 'name' => 'Turkey'],
            ['code' => 'TM', 'name' => 'Turkmenistan'],
            ['code' => 'TC', 'name' => 'Turks and Caicos Islands'],
            ['code' => 'TV', 'name' => 'Tuvalu'],
            ['code' => 'UG', 'name' => 'Uganda'],
            ['code' => 'UA', 'name' => 'Ukraine'],
            ['code' => 'AE', 'name' => 'United Arab Emirates'],
            ['code' => 'GB', 'name' => 'United Kingdom'],
            ['code' => 'CA', 'name' => 'United States'],
            ['code' => 'UM', 'name' => 'United States minor outlying islands'],
            ['code' => 'UY', 'name' => 'Uruguay'],
            ['code' => 'UZ', 'name' => 'Uzbekistan'],
            ['code' => 'VU', 'name' => 'Vanuatu'],
            ['code' => 'VA', 'name' => 'Vatican City State'],
            ['code' => 'VE', 'name' => 'Venezuela'],
            ['code' => 'VN', 'name' => 'Vietnam'],
            ['code' => 'VG', 'name' => 'Virgin Islands (British)'],
            ['code' => 'VI', 'name' => 'Virgin Islands (U.S.)'],
            ['code' => 'WF', 'name' => 'Wallis and Futuna Islands'],
            ['code' => 'EH', 'name' => 'Western Sahara'],
            ['code' => 'YE', 'name' => 'Yemen'],
            ['code' => 'YU', 'name' => 'Yugoslavia'],
            ['code' => 'ZR', 'name' => 'Zaire'],
            ['code' => 'ZM', 'name' => 'Zambia'],
            ['code' => 'ZW', 'name' => 'Zimbabwe'],
        ];
    }

    public function States()
    {
        return $states = [
            // Canada
            ['code' => 'CA', 'abbreviation' => 'AB', 'name' => 'Alberta'],
            ['code' => 'CA', 'abbreviation' => 'BC', 'name' => 'British Columbia'],
            ['code' => 'CA', 'abbreviation' => 'MB', 'name' => 'Manitoba'],
            ['code' => 'CA', 'abbreviation' => 'NB', 'name' => 'New Brunswick'],
            ['code' => 'CA', 'abbreviation' => 'NL', 'name' => 'Newfoundland and Labrador'],
            ['code' => 'CA', 'abbreviation' => 'NT', 'name' => 'Northwest Territories'],
            ['code' => 'CA', 'abbreviation' => 'NS', 'name' => 'Nova Scotia'],
            ['code' => 'CA', 'abbreviation' => 'NU', 'name' => 'Nunavut'],
            ['code' => 'CA', 'abbreviation' => 'ON', 'name' => 'Ontario'],
            ['code' => 'CA', 'abbreviation' => 'PE', 'name' => 'Prince Edward Island'],
            ['code' => 'CA', 'abbreviation' => 'QC', 'name' => 'Quebec'],
            ['code' => 'CA', 'abbreviation' => 'SK', 'name' => 'Saskatchewan'],
            ['code' => 'CA', 'abbreviation' => 'YT', 'name' => 'Yukon'],

            // USA
            ['code' => 'US', 'abbreviation' => 'AL', 'name' => 'Alabama'],
            ['code' => 'US', 'abbreviation' => 'AK', 'name' => 'Alaska'],
            ['code' => 'US', 'abbreviation' => 'AZ', 'name' => 'Arizona'],
            ['code' => 'US', 'abbreviation' => 'AR', 'name' => 'Arkansas'],
            ['code' => 'US', 'abbreviation' => 'CA', 'name' => 'California'],
            ['code' => 'US', 'abbreviation' => 'CO', 'name' => 'Colorado'],
            ['code' => 'US', 'abbreviation' => 'CT', 'name' => 'Connecticut'],
            ['code' => 'US', 'abbreviation' => 'DE', 'name' => 'Delaware'],
            ['code' => 'US', 'abbreviation' => 'FL', 'name' => 'Florida'],
            ['code' => 'US', 'abbreviation' => 'GA', 'name' => 'Georgia'],
            ['code' => 'US', 'abbreviation' => 'HI', 'name' => 'Hawaii'],
            ['code' => 'US', 'abbreviation' => 'ID', 'name' => 'Idaho'],
            ['code' => 'US', 'abbreviation' => 'IL', 'name' => 'Illinois'],
            ['code' => 'US', 'abbreviation' => 'IN', 'name' => 'Indiana'],
            ['code' => 'US', 'abbreviation' => 'IA', 'name' => 'Iowa'],
            ['code' => 'US', 'abbreviation' => 'KS', 'name' => 'Kansas'],
            ['code' => 'US', 'abbreviation' => 'KY', 'name' => 'Kentucky'],
            ['code' => 'US', 'abbreviation' => 'LA', 'name' => 'Louisiana'],
            ['code' => 'US', 'abbreviation' => 'ME', 'name' => 'Maine'],
            ['code' => 'US', 'abbreviation' => 'MD', 'name' => 'Maryland'],
            ['code' => 'US', 'abbreviation' => 'MA', 'name' => 'Massachusetts'],
            ['code' => 'US', 'abbreviation' => 'MI', 'name' => 'Michigan'],
            ['code' => 'US', 'abbreviation' => 'MN', 'name' => 'Minnesota'],
            ['code' => 'US', 'abbreviation' => 'MS', 'name' => 'Mississippi'],
            ['code' => 'US', 'abbreviation' => 'MO', 'name' => 'Missouri'],
            ['code' => 'US', 'abbreviation' => 'MT', 'name' => 'Montana'],
            ['code' => 'US', 'abbreviation' => 'NE', 'name' => 'Nebraska'],
            ['code' => 'US', 'abbreviation' => 'NV', 'name' => 'Nevada'],
            ['code' => 'US', 'abbreviation' => 'NH', 'name' => 'New Hampshire'],
            ['code' => 'US', 'abbreviation' => 'NJ', 'name' => 'New Jersey'],
            ['code' => 'US', 'abbreviation' => 'NM', 'name' => 'New Mexico'],
            ['code' => 'US', 'abbreviation' => 'NY', 'name' => 'New York'],
            ['code' => 'US', 'abbreviation' => 'NC', 'name' => 'NC'],
            ['code' => 'US', 'abbreviation' => 'ND', 'name' => 'North Dakota'],
            ['code' => 'US', 'abbreviation' => 'OH', 'name' => 'Ohio'],
            ['code' => 'US', 'abbreviation' => 'OK', 'name' => 'Oklahoma'],
            ['code' => 'US', 'abbreviation' => 'OR', 'name' => 'Oregon'],
            ['code' => 'US', 'abbreviation' => 'PA', 'name' => 'Pennsylvania'],
            ['code' => 'US', 'abbreviation' => 'RI', 'name' => 'Rhode Island'],
            ['code' => 'US', 'abbreviation' => 'SC', 'name' => 'South Carolina'],
            ['code' => 'US', 'abbreviation' => 'SD', 'name' => 'South Dakota'],
            ['code' => 'US', 'abbreviation' => 'TN', 'name' => 'Tennessee'],
            ['code' => 'US', 'abbreviation' => 'TX', 'name' => 'Texas'],
            ['code' => 'US', 'abbreviation' => 'UT', 'name' => 'Utah'],
            ['code' => 'US', 'abbreviation' => 'VT', 'name' => 'Vermont'],
            ['code' => 'US', 'abbreviation' => 'VA', 'name' => 'Virginia'],
            ['code' => 'US', 'abbreviation' => 'WA', 'name' => 'Washington'],
            ['code' => 'US', 'abbreviation' => 'WV', 'name' => 'West Virginia'],
            ['code' => 'US', 'abbreviation' => 'WI', 'name' => 'Wisconsin'],
            ['code' => 'US', 'abbreviation' => 'WY', 'name' => 'Wyoming'],
            ['code' => 'US', 'abbreviation' => 'AS', 'name' => 'American Samoa'],
            ['code' => 'US', 'abbreviation' => 'DC', 'name' => 'District of Columbia'],
            ['code' => 'US', 'abbreviation' => 'GU', 'name' => 'Guam'],
            ['code' => 'US', 'abbreviation' => 'MP', 'name' => 'Northern Mariana Islands'],
            ['code' => 'US', 'abbreviation' => 'PR', 'name' => 'Puerto Rico'],
            ['code' => 'US', 'abbreviation' => 'VI', 'name' => 'United States Virgin Islands'],
        ];
    }

    public function InputType()
    {
        return [
            ['name' => 'tel', 'mask' => ''],
            ['name' => 'email', 'mask' => ''],
            ['name' => 'city', 'mask' => ''],
            ['name' => 'custom', 'mask' => ''],
            ['name' => 'checkbox', 'mask' => ''],
            ['name' => 'color', 'mask' => ''],
            ['name' => 'date', 'mask' => ''],
            ['name' => 'datetime-local', 'mask' => ''],
            ['name' => 'file', 'mask' => ''],
            ['name' => 'hidden', 'mask' => ''],
            ['name' => 'image', 'mask' => ''],
            ['name' => 'map', 'mask' => ''],
            ['name' => 'month', 'mask' => ''],
            ['name' => 'number', 'mask' => ''],
            ['name' => 'password', 'mask' => ''],
            ['name' => 'radio', 'mask' => ''],
            ['name' => 'range', 'mask' => ''],
            ['name' => 'select', 'mask' => ''],
            ['name' => 'select_mulitple', 'mask' => ''],
            ['name' => 'tel', 'mask' => ''],
            ['name' => 'text', 'mask' => ''],
            ['name' => 'time', 'mask' => ''],
            ['name' => 'url', 'mask' => ''],
            ['name' => 'week', 'mask' => ''],
            ['name' => 'textarea', 'mask' => ''],
            ['name' => 'video', 'mask' => ''],
            ['name' => 'city', 'mask' => ''],
            ['name' => 'zip', 'mask' => ''],
            ['name' => 'address', 'mask' => ''],
            ['name' => 'related', 'mask' => ''],
        ];
    }
}
