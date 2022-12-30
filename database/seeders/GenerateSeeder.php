<?php

namespace Database\Seeders;


use App\Models\ModuleConvertable;
use App\Models\WorkFlowData;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\Module;
use Illuminate\Support\Facades\DB as DB;
use App\Models\User;
use App\Models\DataletType;
use App\Models\Datalet;
use App\Models\Field;
use App\Models\Permission;

class GenerateSeeder extends Seeder
{
    /**
     * Generates tables, fields and data lists.
     *
     * @return void
     */
    public function run()
    {
        $seedAmount=50;
        Log::info("Generating users");
        $this->AddUsers();
        $this->AddSettings();

        Log::info("Add Workflow Actions");
       // $this->addWorkflowActions();

        Log::info("Add Datalet Types");
        $this->addDataletTypes();
        $this->addDatalets();

        Log::info("Generating module");
        $module = new Module;
        $module->generate($seedAmount);


        Log::info("Generating static lists");

         $faker = Factory::create();
        $current=$this;
        Module::where('status', 1)
            ->where('create_table', 1)
            ->where('faker_seed', 0)
            ->get()
            ->each(function ($module) use ($current, $faker) {
                if(method_exists($current, $module->name)) {
                    Log::info("Generating module: " . $module->name);
                    $table=strtolower($module->name);
                    DB::table($table)->truncate();
                    $data=$current->{$module->name}();
                    foreach($data as $row) {
                        $row['slug']=bin2hex(random_bytes(16));
                        DB::table($table)->insert($row);
                    }
                }
            });

        $this->addModulesAndRoles();
        $this->sampleMedia();
    }



    private function sampleMedia()
    {
        $file=file_get_contents('http://demo.iceburg.ca/seed/video/christmasornaments.mp4');
        if($file){
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
                    'slug' => 'dsfsdfsdfs',
                    'status' => 4,
                    'soft_delete' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        }

        $data['created_at']=date('Y-m-d H:i:s', strtotime("-" . rand(1, 31) . " DAY"));
        $data['updated_at']=$data['created_at'];


        $file=file_get_contents('http://demo.iceburg.ca/seed/recording/sample.ogg');
        //           'audio_recording' => 'data:audio/ogg;base64,'.base64_encode($file),
        if($file){
            DB::table('meetings')->insert(
                ['name' => 'Client Meeting',
                    'description' => 'This is a typical service call',
                    'start_date' => strtotime('NOW'),
                    'end_date' => strtotime('NOW'),
                    'location' => 'Office',
                    'audio_recording' => 'data:audio/ogg;base64,'.base64_encode($file),
                    'types' => 1,
                    'assigned_to' => 1,
                    'slug' => 'dsfsdfsdfss',
                    'status' => 4,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            );
        }

        Log::info("Finished Seeding sample media");
    }

    private function addModulesAndRoles()
    {
        $module = Module::where('name', 'roles')->first();
        $records=DB::table($module->name)->get();
        Permission::truncate();
        foreach($records as $record)
        {
            Module::all()->each(function ($module) use ($record) {
                Permission::insert([
                   'role_id' => $record->id,
                   'module_id' => $module->id
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
            ['id' => 6, 'name' => 'Latest Campaign'],
            ['id' => 7, 'name' => 'Latest Meetings'],
        ]);
    }

    private function addDataLets()
    {
        Datalet::truncate();
        Datalet::insert([

            ['type'=>1,
                'module_id'=>0,
                'label' => 'Total Sales',
                'size' => 12,
                'display_order' => 1],
            ['type'=>2,
                'module_id'=>0,
                'label' => 'Number of new Leads / Contacts / Accounts over the last 7 Days',
                'size' => 12,
                'display_order' => 1],
            ['type'=>3,
                'module_id'=>0,
                'label' => 'Meetings',
                'size' => 12,
                'display_order' => 1],
            ['type'=>4,
                'module_id'=>0,
                'label' => 'Number of new Opportunities / Quotes / Contracts over the last 7 Days',
                'size' => 12,
                'display_order' => 1],
            ['type'=>1,
                'module_id'=>0,
                'label' => 'Orders This Month',
                'size' => 12,
                'display_order' => 1],
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
                ['name' => 'Field Change Status']
            ]
        );
    }

    private function AddUsers()
    {
        User::truncate();
        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'Admin',
            'email' => 'admin@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('admin'),
            'role_id' => 1
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'User',
            'email' => 'user@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('user'),
            'role_id' => 2
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'Sales',
            'email' => 'sales@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('sales'),
            'role_id' => 3
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'Accounting',
            'email' => 'accounting@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('accounting'),
            'role_id' => 4
        ]);

        $image = file_get_contents('http://demo.iceburg.ca/seed/people/0000' . rand(10,99) . '.jpg');
        $userId=DB::table('users')->insertGetId([
            'name' => 'Marketing',
            'email' => 'marketing@iceburg.ca',
            'profile_pic' => 'data:image/jpg;base64,' . base64_encode($image),
            'password' => bcrypt('marketing'),
            'role_id' => 5
        ]);
    }

    private function AddSettings()
    {
        Log::info("Generating Settings");
        DB::table('settings')->insert([
            'name' => 'theme',
            'value' => 'light'
        ]);

        DB::table('settings')->insert([
            'name' => 'search_per_page',
            'value' => '10'
        ]);

        DB::table('settings')->insert([
            'name' => 'submodule_search_per_page',
            'value' => '10'
        ]);

        DB::table('settings')->insert([
            'name' => 'title',
            'value' => 'Iceburg CRM'
        ]);

        DB::table('settings')->insert([
            'name' => 'description',
            'value' => 'Open Source, data driven, extendable, unlimited relationships, convertable modules, 29 default themes, light/dark themes'
        ]);

        DB::table('settings')->insert([
            'name' => 'max_export_records',
            'value' => 10000
        ]);



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

    private function Roles()
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
            ['code' =>'AFN' , 'name' => 'Afghani', 'symbol' => '؋' ],
            ['code' =>'ALL' , 'name' => 'Lek', 'symbol' => 'Lek' ],
            ['code' =>'ANG' , 'name' => 'Netherlands Antillian Guilder', 'symbol' => 'ƒ' ],
            ['code' =>'ARS' , 'name' => 'Argentine Peso', 'symbol' => '$' ],
            ['code' =>'AUD' , 'name' => 'Australian Dollar', 'symbol' => '$' ],
            ['code' =>'AWG' , 'name' => 'Aruban Guilder', 'symbol' => 'ƒ' ],
            ['code' =>'AZN' , 'name' => 'Azerbaijanian Manat', 'symbol' => 'ман' ],
            ['code' =>'BAM' , 'name' => 'Convertible Marks', 'symbol' => 'KM' ],
            ['code' => 'BDT', 'name' => 'Bangladeshi Taka', 'symbol' => '৳'],
            ['code' =>'BBD' , 'name' => 'Barbados Dollar', 'symbol' => '$' ],
            ['code' =>'BGN' , 'name' => 'Bulgarian Lev', 'symbol' => 'лв' ],
            ['code' =>'BMD' , 'name' => 'Bermudian Dollar', 'symbol' => '$' ],
            ['code' =>'BND' , 'name' => 'Brunei Dollar', 'symbol' => '$' ],
            ['code' =>'BOB' , 'name' => 'BOV Boliviano Mvdol', 'symbol' => '$b' ],
            ['code' =>'BRL' , 'name' => 'Brazilian Real', 'symbol' => 'R$' ],
            ['code' =>'BSD' , 'name' => 'Bahamian Dollar', 'symbol' => '$' ],
            ['code' =>'BWP' , 'name' => 'Pula', 'symbol' => 'P' ],
            ['code' =>'BYR' , 'name' => 'Belarussian Ruble', 'symbol' => '₽' ],
            ['code' =>'BZD' , 'name' => 'Belize Dollar', 'symbol' => 'BZ$' ],
            ['code' =>'CAD' , 'name' => 'Canadian Dollar', 'symbol' => '$' ],
            ['code' =>'CHF' , 'name' => 'Swiss Franc', 'symbol' => 'CHF' ],
            ['code' =>'CLP' , 'name' => 'CLF Chilean Peso Unidades de fomento', 'symbol' => '$' ],
            ['code' =>'CNY' , 'name' => 'Yuan Renminbi', 'symbol' => '¥' ],
            ['code' =>'COP' , 'name' => 'COU Colombian Peso Unidad de Valor Real', 'symbol' => '$' ],
            ['code' =>'CRC' , 'name' => 'Costa Rican Colon', 'symbol' => '₡' ],
            ['code' =>'CUP' , 'name' => 'CUC Cuban Peso Peso Convertible', 'symbol' => '₱' ],
            ['code' =>'CZK' , 'name' => 'Czech Koruna', 'symbol' => 'Kč' ],
            ['code' =>'DKK' , 'name' => 'Danish Krone', 'symbol' => 'kr' ],
            ['code' =>'DOP' , 'name' => 'Dominican Peso', 'symbol' => 'RD$' ],
            ['code' =>'EGP' , 'name' => 'Egyptian Pound', 'symbol' => '£' ],
            ['code' =>'EUR' , 'name' => 'Euro', 'symbol' => '€' ],
            ['code' =>'FJD' , 'name' => 'Fiji Dollar', 'symbol' => '$' ],
            ['code' =>'FKP' , 'name' => 'Falkland Islands Pound', 'symbol' => '£' ],
            ['code' =>'GBP' , 'name' => 'Pound Sterling', 'symbol' => '£' ],
            ['code' =>'GIP' , 'name' => 'Gibraltar Pound', 'symbol' => '£' ],
            ['code' =>'GTQ' , 'name' => 'Quetzal', 'symbol' => 'Q' ],
            ['code' =>'GYD' , 'name' => 'Guyana Dollar', 'symbol' => '$' ],
            ['code' =>'HKD' , 'name' => 'Hong Kong Dollar', 'symbol' => '$' ],
            ['code' =>'HNL' , 'name' => 'Lempira', 'symbol' => 'L' ],
            ['code' =>'HRK' , 'name' => 'Croatian Kuna', 'symbol' => 'kn' ],
            ['code' =>'HUF' , 'name' => 'Forint', 'symbol' => 'Ft' ],
            ['code' =>'IDR' , 'name' => 'Rupiah', 'symbol' => 'Rp' ],
            ['code' =>'ILS' , 'name' => 'New Israeli Sheqel', 'symbol' => '₪' ],
            ['code' =>'IRR' , 'name' => 'Iranian Rial', 'symbol' => '﷼' ],
            ['code' =>'ISK' , 'name' => 'Iceland Krona', 'symbol' => 'kr' ],
            ['code' =>'JMD' , 'name' => 'Jamaican Dollar', 'symbol' => 'J$' ],
            ['code' =>'JPY' , 'name' => 'Yen', 'symbol' => '¥' ],
            ['code' =>'KGS' , 'name' => 'Som', 'symbol' => 'лв' ],
            ['code' =>'KHR' , 'name' => 'Riel', 'symbol' => '៛' ],
            ['code' =>'KPW' , 'name' => 'North Korean Won', 'symbol' => '₩' ],
            ['code' =>'KRW' , 'name' => 'Won', 'symbol' => '₩' ],
            ['code' =>'KYD' , 'name' => 'Cayman Islands Dollar', 'symbol' => '$' ],
            ['code' =>'KZT' , 'name' => 'Tenge', 'symbol' => 'лв' ],
            ['code' =>'LAK' , 'name' => 'Kip', 'symbol' => '₭' ],
            ['code' =>'LBP' , 'name' => 'Lebanese Pound', 'symbol' => '£' ],
            ['code' =>'LKR' , 'name' => 'Sri Lanka Rupee', 'symbol' => '₨' ],
            ['code' =>'LRD' , 'name' => 'Liberian Dollar', 'symbol' => '$' ],
            ['code' =>'LTL' , 'name' => 'Lithuanian Litas', 'symbol' => 'Lt' ],
            ['code' =>'LVL' , 'name' => 'Latvian Lats', 'symbol' => 'Ls' ],
            ['code' =>'MKD' , 'name' => 'Denar', 'symbol' => 'ден' ],
            ['code' =>'MNT' , 'name' => 'Tugrik', 'symbol' => '₮' ],
            ['code' =>'MUR' , 'name' => 'Mauritius Rupee', 'symbol' => '₨' ],
            ['code' =>'MXN' , 'name' => 'MXV Mexican Peso Mexican Unidad de Inversion (UDI]', 'symbol' => '$' ],
            ['code' =>'MYR' , 'name' => 'Malaysian Ringgit', 'symbol' => 'RM' ],
            ['code' =>'MZN' , 'name' => 'Metical', 'symbol' => 'MT' ],
            ['code' =>'NGN' , 'name' => 'Naira', 'symbol' => '₦' ],
            ['code' =>'NIO' , 'name' => 'Cordoba Oro', 'symbol' => 'C$' ],
            ['code' =>'NOK' , 'name' => 'Norwegian Krone', 'symbol' => 'kr' ],
            ['code' =>'NPR' , 'name' => 'Nepalese Rupee', 'symbol' => '₨' ],
            ['code' =>'NZD' , 'name' => 'New Zealand Dollar', 'symbol' => '$' ],
            ['code' =>'OMR' , 'name' => 'Rial Omani', 'symbol' => '﷼' ],
            ['code' =>'PAB' , 'name' => 'USD Balboa US Dollar', 'symbol' => 'B/.' ],
            ['code' =>'PEN' , 'name' => 'Nuevo Sol', 'symbol' => 'S/.' ],
            ['code' =>'PHP' , 'name' => 'Philippine Peso', 'symbol' => 'Php' ],
            ['code' =>'PKR' , 'name' => 'Pakistan Rupee', 'symbol' => '₨' ],
            ['code' =>'PLN' , 'name' => 'Zloty', 'symbol' => 'zł' ],
            ['code' =>'PYG' , 'name' => 'Guarani', 'symbol' => 'Gs' ],
            ['code' =>'QAR' , 'name' => 'Qatari Rial', 'symbol' => '﷼' ],
            ['code' =>'RON' , 'name' => 'New Leu', 'symbol' => 'lei' ],
            ['code' =>'RSD' , 'name' => 'Serbian Dinar', 'symbol' => 'Дин.' ],
            ['code' =>'RUB' , 'name' => 'Russian Ruble', 'symbol' => 'руб' ],
            ['code' =>'SAR' , 'name' => 'Saudi Riyal', 'symbol' => '﷼' ],
            ['code' =>'SBD' , 'name' => 'Solomon Islands Dollar', 'symbol' => '$' ],
            ['code' =>'SCR' , 'name' => 'Seychelles Rupee', 'symbol' => '₨' ],
            ['code' =>'SEK' , 'name' => 'Swedish Krona', 'symbol' => 'kr' ],
            ['code' =>'SGD' , 'name' => 'Singapore Dollar', 'symbol' => '$' ],
            ['code' =>'SHP' , 'name' => 'Saint Helena Pound', 'symbol' => '£' ],
            ['code' =>'SOS' , 'name' => 'Somali Shilling', 'symbol' => 'S' ],
            ['code' =>'SRD' , 'name' => 'Surinam Dollar', 'symbol' => '$' ],
            ['code' =>'SVC' , 'name' => 'USD El Salvador Colon US Dollar', 'symbol' => '$' ],
            ['code' =>'SYP' , 'name' => 'Syrian Pound', 'symbol' => '£' ],
            ['code' =>'THB' , 'name' => 'Baht', 'symbol' => '฿' ],
            ['code' =>'TRY' , 'name' => 'Turkish Lira', 'symbol' => 'TL' ],
            ['code' =>'TTD' , 'name' => 'Trinidad and Tobago Dollar', 'symbol' => 'TT$' ],
            ['code' =>'TWD' , 'name' => 'New Taiwan Dollar', 'symbol' => 'NT$' ],
            ['code' =>'UAH' , 'name' => 'Hryvnia', 'symbol' => '₴' ],
            ['code' =>'USD' , 'name' => 'US Dollar', 'symbol' => '$' ],
            ['code' =>'UYU' , 'name' => 'UYI Uruguay Peso en Unidades Indexadas', 'symbol' => '$U' ],
            ['code' =>'UZS' , 'name' => 'Uzbekistan Sum', 'symbol' => 'лв' ],
            ['code' =>'VEF' , 'name' => 'Bolivar Fuerte', 'symbol' => 'Bs' ],
            ['code' =>'VND' , 'name' => 'Dong', 'symbol' => '₫' ],
            ['code' =>'XCD' , 'name' => 'East Caribbean Dollar', 'symbol' => '$' ],
            ['code' =>'YER' , 'name' => 'Yemeni Rial', 'symbol' => '﷼' ],
            ['code' =>'ZAR' , 'name' => 'Rand', 'symbol' => 'R' ],
        ];
    }

    private function Themes()
    {
        return [
            ['name' => "light"],
            ['name' => "dark"],
            ['name' => "cupcake"],
            ['name' => "bumblebee"],
            ['name' => "emerald"],
            ['name' => "corporate"],
            ['name' => "synthwave"],
            ['name' => "retro"],
            ['name' => "cyberpunk"],
            ['name' => "valentine"],
            ['name' => "halloween"],
            ['name' => "garden"],
            ['name' => "forest"],
            ['name' => "aqua"],
            ['name' => "lofi"],
            ['name' => "pastel"],
            ['name' => "fantasy"],
            ['name' => "wireframe"],
            ['name' => "black"],
            ['name' => "luxury"],
            ['name' => "dracula"],
            ['name' => "cmyk"],
            ['name' => "autumn"],
            ['name' => "business"],
            ['name' => "acid"],
            ['name' => "lemonade"],
            ['name' => "night"],
            ['name' => "coffee"],
            ['name' => "winter"],
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
        return array(
            array('code' => 'US', 'name' => 'Canada'),
            array('code' => 'AF', 'name' => 'Afghanistan'),
            array('code' => 'AL', 'name' => 'Albania'),
            array('code' => 'DZ', 'name' => 'Algeria'),
            array('code' => 'AS', 'name' => 'American Samoa'),
            array('code' => 'AD', 'name' => 'Andorra'),
            array('code' => 'AO', 'name' => 'Angola'),
            array('code' => 'AI', 'name' => 'Anguilla'),
            array('code' => 'AQ', 'name' => 'Antarctica'),
            array('code' => 'AG', 'name' => 'Antigua and/or Barbuda'),
            array('code' => 'AR', 'name' => 'Argentina'),
            array('code' => 'AM', 'name' => 'Armenia'),
            array('code' => 'AW', 'name' => 'Aruba'),
            array('code' => 'AU', 'name' => 'Australia'),
            array('code' => 'AT', 'name' => 'Austria'),
            array('code' => 'AZ', 'name' => 'Azerbaijan'),
            array('code' => 'BS', 'name' => 'Bahamas'),
            array('code' => 'BH', 'name' => 'Bahrain'),
            array('code' => 'BD', 'name' => 'Bangladesh'),
            array('code' => 'BB', 'name' => 'Barbados'),
            array('code' => 'BY', 'name' => 'Belarus'),
            array('code' => 'BE', 'name' => 'Belgium'),
            array('code' => 'BZ', 'name' => 'Belize'),
            array('code' => 'BJ', 'name' => 'Benin'),
            array('code' => 'BM', 'name' => 'Bermuda'),
            array('code' => 'BT', 'name' => 'Bhutan'),
            array('code' => 'BO', 'name' => 'Bolivia'),
            array('code' => 'BA', 'name' => 'Bosnia and Herzegovina'),
            array('code' => 'BW', 'name' => 'Botswana'),
            array('code' => 'BV', 'name' => 'Bouvet Island'),
            array('code' => 'BR', 'name' => 'Brazil'),
            array('code' => 'IO', 'name' => 'British lndian Ocean Territory'),
            array('code' => 'BN', 'name' => 'Brunei Darussalam'),
            array('code' => 'BG', 'name' => 'Bulgaria'),
            array('code' => 'BF', 'name' => 'Burkina Faso'),
            array('code' => 'BI', 'name' => 'Burundi'),
            array('code' => 'KH', 'name' => 'Cambodia'),
            array('code' => 'CM', 'name' => 'Cameroon'),
            array('code' => 'CV', 'name' => 'Cape Verde'),
            array('code' => 'KY', 'name' => 'Cayman Islands'),
            array('code' => 'CF', 'name' => 'Central African Republic'),
            array('code' => 'TD', 'name' => 'Chad'),
            array('code' => 'CL', 'name' => 'Chile'),
            array('code' => 'CN', 'name' => 'China'),
            array('code' => 'CX', 'name' => 'Christmas Island'),
            array('code' => 'CC', 'name' => 'Cocos (Keeling) Islands'),
            array('code' => 'CO', 'name' => 'Colombia'),
            array('code' => 'KM', 'name' => 'Comoros'),
            array('code' => 'CG', 'name' => 'Congo'),
            array('code' => 'CK', 'name' => 'Cook Islands'),
            array('code' => 'CR', 'name' => 'Costa Rica'),
            array('code' => 'HR', 'name' => 'Croatia (Hrvatska)'),
            array('code' => 'CU', 'name' => 'Cuba'),
            array('code' => 'CY', 'name' => 'Cyprus'),
            array('code' => 'CZ', 'name' => 'Czech Republic'),
            array('code' => 'DK', 'name' => 'Denmark'),
            array('code' => 'DJ', 'name' => 'Djibouti'),
            array('code' => 'DM', 'name' => 'Dominica'),
            array('code' => 'DO', 'name' => 'Dominican Republic'),
            array('code' => 'TP', 'name' => 'East Timor'),
            array('code' => 'EC', 'name' => 'Ecudaor'),
            array('code' => 'EG', 'name' => 'Egypt'),
            array('code' => 'SV', 'name' => 'El Salvador'),
            array('code' => 'GQ', 'name' => 'Equatorial Guinea'),
            array('code' => 'ER', 'name' => 'Eritrea'),
            array('code' => 'EE', 'name' => 'Estonia'),
            array('code' => 'ET', 'name' => 'Ethiopia'),
            array('code' => 'FK', 'name' => 'Falkland Islands (Malvinas)'),
            array('code' => 'FO', 'name' => 'Faroe Islands'),
            array('code' => 'FJ', 'name' => 'Fiji'),
            array('code' => 'FI', 'name' => 'Finland'),
            array('code' => 'FR', 'name' => 'France'),
            array('code' => 'FX', 'name' => 'France, Metropolitan'),
            array('code' => 'GF', 'name' => 'French Guiana'),
            array('code' => 'PF', 'name' => 'French Polynesia'),
            array('code' => 'TF', 'name' => 'French Southern Territories'),
            array('code' => 'GA', 'name' => 'Gabon'),
            array('code' => 'GM', 'name' => 'Gambia'),
            array('code' => 'GE', 'name' => 'Georgia'),
            array('code' => 'DE', 'name' => 'Germany'),
            array('code' => 'GH', 'name' => 'Ghana'),
            array('code' => 'GI', 'name' => 'Gibraltar'),
            array('code' => 'GR', 'name' => 'Greece'),
            array('code' => 'GL', 'name' => 'Greenland'),
            array('code' => 'GD', 'name' => 'Grenada'),
            array('code' => 'GP', 'name' => 'Guadeloupe'),
            array('code' => 'GU', 'name' => 'Guam'),
            array('code' => 'GT', 'name' => 'Guatemala'),
            array('code' => 'GN', 'name' => 'Guinea'),
            array('code' => 'GW', 'name' => 'Guinea-Bissau'),
            array('code' => 'GY', 'name' => 'Guyana'),
            array('code' => 'HT', 'name' => 'Haiti'),
            array('code' => 'HM', 'name' => 'Heard and Mc Donald Islands'),
            array('code' => 'HN', 'name' => 'Honduras'),
            array('code' => 'HK', 'name' => 'Hong Kong'),
            array('code' => 'HU', 'name' => 'Hungary'),
            array('code' => 'IS', 'name' => 'Iceland'),
            array('code' => 'IN', 'name' => 'India'),
            array('code' => 'ID', 'name' => 'Indonesia'),
            array('code' => 'IR', 'name' => 'Iran (Islamic Republic of)'),
            array('code' => 'IQ', 'name' => 'Iraq'),
            array('code' => 'IE', 'name' => 'Ireland'),
            array('code' => 'IL', 'name' => 'Israel'),
            array('code' => 'IT', 'name' => 'Italy'),
            array('code' => 'CI', 'name' => 'Ivory Coast'),
            array('code' => 'JM', 'name' => 'Jamaica'),
            array('code' => 'JP', 'name' => 'Japan'),
            array('code' => 'JO', 'name' => 'Jordan'),
            array('code' => 'KZ', 'name' => 'Kazakhstan'),
            array('code' => 'KE', 'name' => 'Kenya'),
            array('code' => 'KI', 'name' => 'Kiribati'),
            array('code' => 'KP', 'name' => 'Korea, Democratic People\'s Republic of'),
            array('code' => 'KR', 'name' => 'Korea, Republic of'),
            array('code' => 'KW', 'name' => 'Kuwait'),
            array('code' => 'KG', 'name' => 'Kyrgyzstan'),
            array('code' => 'LA', 'name' => 'Lao People\'s Democratic Republic'),
            array('code' => 'LV', 'name' => 'Latvia'),
            array('code' => 'LB', 'name' => 'Lebanon'),
            array('code' => 'LS', 'name' => 'Lesotho'),
            array('code' => 'LR', 'name' => 'Liberia'),
            array('code' => 'LY', 'name' => 'Libyan Arab Jamahiriya'),
            array('code' => 'LI', 'name' => 'Liechtenstein'),
            array('code' => 'LT', 'name' => 'Lithuania'),
            array('code' => 'LU', 'name' => 'Luxembourg'),
            array('code' => 'MO', 'name' => 'Macau'),
            array('code' => 'MK', 'name' => 'Macedonia'),
            array('code' => 'MG', 'name' => 'Madagascar'),
            array('code' => 'MW', 'name' => 'Malawi'),
            array('code' => 'MY', 'name' => 'Malaysia'),
            array('code' => 'MV', 'name' => 'Maldives'),
            array('code' => 'ML', 'name' => 'Mali'),
            array('code' => 'MT', 'name' => 'Malta'),
            array('code' => 'MH', 'name' => 'Marshall Islands'),
            array('code' => 'MQ', 'name' => 'Martinique'),
            array('code' => 'MR', 'name' => 'Mauritania'),
            array('code' => 'MU', 'name' => 'Mauritius'),
            array('code' => 'TY', 'name' => 'Mayotte'),
            array('code' => 'MX', 'name' => 'Mexico'),
            array('code' => 'FM', 'name' => 'Micronesia, Federated States of'),
            array('code' => 'MD', 'name' => 'Moldova, Republic of'),
            array('code' => 'MC', 'name' => 'Monaco'),
            array('code' => 'MN', 'name' => 'Mongolia'),
            array('code' => 'MS', 'name' => 'Montserrat'),
            array('code' => 'MA', 'name' => 'Morocco'),
            array('code' => 'MZ', 'name' => 'Mozambique'),
            array('code' => 'MM', 'name' => 'Myanmar'),
            array('code' => 'NA', 'name' => 'Namibia'),
            array('code' => 'NR', 'name' => 'Nauru'),
            array('code' => 'NP', 'name' => 'Nepal'),
            array('code' => 'NL', 'name' => 'Netherlands'),
            array('code' => 'AN', 'name' => 'Netherlands Antilles'),
            array('code' => 'NC', 'name' => 'New Caledonia'),
            array('code' => 'NZ', 'name' => 'New Zealand'),
            array('code' => 'NI', 'name' => 'Nicaragua'),
            array('code' => 'NE', 'name' => 'Niger'),
            array('code' => 'NG', 'name' => 'Nigeria'),
            array('code' => 'NU', 'name' => 'Niue'),
            array('code' => 'NF', 'name' => 'Norfork Island'),
            array('code' => 'MP', 'name' => 'Northern Mariana Islands'),
            array('code' => 'NO', 'name' => 'Norway'),
            array('code' => 'OM', 'name' => 'Oman'),
            array('code' => 'PK', 'name' => 'Pakistan'),
            array('code' => 'PW', 'name' => 'Palau'),
            array('code' => 'PA', 'name' => 'Panama'),
            array('code' => 'PG', 'name' => 'Papua New Guinea'),
            array('code' => 'PY', 'name' => 'Paraguay'),
            array('code' => 'PE', 'name' => 'Peru'),
            array('code' => 'PH', 'name' => 'Philippines'),
            array('code' => 'PN', 'name' => 'Pitcairn'),
            array('code' => 'PL', 'name' => 'Poland'),
            array('code' => 'PT', 'name' => 'Portugal'),
            array('code' => 'PR', 'name' => 'Puerto Rico'),
            array('code' => 'QA', 'name' => 'Qatar'),
            array('code' => 'RE', 'name' => 'Reunion'),
            array('code' => 'RO', 'name' => 'Romania'),
            array('code' => 'RU', 'name' => 'Russian Federation'),
            array('code' => 'RW', 'name' => 'Rwanda'),
            array('code' => 'KN', 'name' => 'Saint Kitts and Nevis'),
            array('code' => 'LC', 'name' => 'Saint Lucia'),
            array('code' => 'VC', 'name' => 'Saint Vincent and the Grenadines'),
            array('code' => 'WS', 'name' => 'Samoa'),
            array('code' => 'SM', 'name' => 'San Marino'),
            array('code' => 'ST', 'name' => 'Sao Tome and Principe'),
            array('code' => 'SA', 'name' => 'Saudi Arabia'),
            array('code' => 'SN', 'name' => 'Senegal'),
            array('code' => 'RS', 'name' => 'Serbia'),
            array('code' => 'SC', 'name' => 'Seychelles'),
            array('code' => 'SL', 'name' => 'Sierra Leone'),
            array('code' => 'SG', 'name' => 'Singapore'),
            array('code' => 'SK', 'name' => 'Slovakia'),
            array('code' => 'SI', 'name' => 'Slovenia'),
            array('code' => 'SB', 'name' => 'Solomon Islands'),
            array('code' => 'SO', 'name' => 'Somalia'),
            array('code' => 'ZA', 'name' => 'South Africa'),
            array('code' => 'GS', 'name' => 'South Georgia South Sandwich Islands'),
            array('code' => 'ES', 'name' => 'Spain'),
            array('code' => 'LK', 'name' => 'Sri Lanka'),
            array('code' => 'SH', 'name' => 'St. Helena'),
            array('code' => 'PM', 'name' => 'St. Pierre and Miquelon'),
            array('code' => 'SD', 'name' => 'Sudan'),
            array('code' => 'SR', 'name' => 'Suriname'),
            array('code' => 'SJ', 'name' => 'Svalbarn and Jan Mayen Islands'),
            array('code' => 'SZ', 'name' => 'Swaziland'),
            array('code' => 'SE', 'name' => 'Sweden'),
            array('code' => 'CH', 'name' => 'Switzerland'),
            array('code' => 'SY', 'name' => 'Syrian Arab Republic'),
            array('code' => 'TW', 'name' => 'Taiwan'),
            array('code' => 'TJ', 'name' => 'Tajikistan'),
            array('code' => 'TZ', 'name' => 'Tanzania, United Republic of'),
            array('code' => 'TH', 'name' => 'Thailand'),
            array('code' => 'TG', 'name' => 'Togo'),
            array('code' => 'TK', 'name' => 'Tokelau'),
            array('code' => 'TO', 'name' => 'Tonga'),
            array('code' => 'TT', 'name' => 'Trinidad and Tobago'),
            array('code' => 'TN', 'name' => 'Tunisia'),
            array('code' => 'TR', 'name' => 'Turkey'),
            array('code' => 'TM', 'name' => 'Turkmenistan'),
            array('code' => 'TC', 'name' => 'Turks and Caicos Islands'),
            array('code' => 'TV', 'name' => 'Tuvalu'),
            array('code' => 'UG', 'name' => 'Uganda'),
            array('code' => 'UA', 'name' => 'Ukraine'),
            array('code' => 'AE', 'name' => 'United Arab Emirates'),
            array('code' => 'GB', 'name' => 'United Kingdom'),
            array('code' => 'CA', 'name' => 'United States'),
            array('code' => 'UM', 'name' => 'United States minor outlying islands'),
            array('code' => 'UY', 'name' => 'Uruguay'),
            array('code' => 'UZ', 'name' => 'Uzbekistan'),
            array('code' => 'VU', 'name' => 'Vanuatu'),
            array('code' => 'VA', 'name' => 'Vatican City State'),
            array('code' => 'VE', 'name' => 'Venezuela'),
            array('code' => 'VN', 'name' => 'Vietnam'),
            array('code' => 'VG', 'name' => 'Virgin Islands (British)'),
            array('code' => 'VI', 'name' => 'Virgin Islands (U.S.)'),
            array('code' => 'WF', 'name' => 'Wallis and Futuna Islands'),
            array('code' => 'EH', 'name' => 'Western Sahara'),
            array('code' => 'YE', 'name' => 'Yemen'),
            array('code' => 'YU', 'name' => 'Yugoslavia'),
            array('code' => 'ZR', 'name' => 'Zaire'),
            array('code' => 'ZM', 'name' => 'Zambia'),
            array('code' => 'ZW', 'name' => 'Zimbabwe'),
        );
    }

    public function States()
    {
        return  $states = array(
            // Canada
            array('code' => 'CA', 'abbreviation' => 'AB', 'name' => 'Alberta'),
            array('code' => 'CA', 'abbreviation' => 'BC', 'name' => 'British Columbia'),
            array('code' => 'CA', 'abbreviation' => 'MB', 'name' => 'Manitoba'),
            array('code' => 'CA', 'abbreviation' => 'NB', 'name' => 'New Brunswick'),
            array('code' => 'CA', 'abbreviation' => 'NL', 'name' => 'Newfoundland and Labrador'),
            array('code' => 'CA', 'abbreviation' => 'NT', 'name' => 'Northwest Territories'),
            array('code' => 'CA', 'abbreviation' => 'NS', 'name' => 'Nova Scotia'),
            array('code' => 'CA', 'abbreviation' => 'NU', 'name' => 'Nunavut'),
            array('code' => 'CA', 'abbreviation' => 'ON', 'name' => 'Ontario'),
            array('code' => 'CA', 'abbreviation' => 'PE', 'name' => 'Prince Edward Island'),
            array('code' => 'CA', 'abbreviation' => 'QC', 'name' => 'Quebec'),
            array('code' => 'CA', 'abbreviation' => 'SK', 'name' => 'Saskatchewan'),
            array('code' => 'CA', 'abbreviation' => 'YT', 'name' => 'Yukon'),

            // USA
            array('code' => 'US', 'abbreviation' => 'AL', 'name' => 'Alabama'),
            array('code' => 'US', 'abbreviation' => 'AK', 'name' => 'Alaska'),
            array('code' => 'US', 'abbreviation' => 'AZ', 'name' => 'Arizona'),
            array('code' => 'US', 'abbreviation' => 'AR', 'name' => 'Arkansas'),
            array('code' => 'US', 'abbreviation' => 'CA', 'name' => 'California'),
            array('code' => 'US', 'abbreviation' => 'CO', 'name' => 'Colorado'),
            array('code' => 'US', 'abbreviation' => 'CT', 'name' => 'Connecticut'),
            array('code' => 'US', 'abbreviation' => 'DE', 'name' => 'Delaware'),
            array('code' => 'US', 'abbreviation' => 'FL', 'name' => 'Florida'),
            array('code' => 'US', 'abbreviation' => 'GA', 'name' => 'Georgia'),
            array('code' => 'US', 'abbreviation' => 'HI', 'name' => 'Hawaii'),
            array('code' => 'US', 'abbreviation' => 'ID', 'name' => 'Idaho'),
            array('code' => 'US', 'abbreviation' => 'IL', 'name' => 'Illinois'),
            array('code' => 'US', 'abbreviation' => 'IN', 'name' => 'Indiana'),
            array('code' => 'US', 'abbreviation' => 'IA', 'name' => 'Iowa'),
            array('code' => 'US', 'abbreviation' => 'KS', 'name' => 'Kansas'),
            array('code' => 'US', 'abbreviation' => 'KY', 'name' => 'Kentucky'),
            array('code' => 'US', 'abbreviation' => 'LA', 'name' => 'Louisiana'),
            array('code' => 'US', 'abbreviation' => 'ME', 'name' => 'Maine'),
            array('code' => 'US', 'abbreviation' => 'MD', 'name' => 'Maryland'),
            array('code' => 'US', 'abbreviation' => 'MA', 'name' => 'Massachusetts'),
            array('code' => 'US', 'abbreviation' => 'MI', 'name' => 'Michigan'),
            array('code' => 'US', 'abbreviation' => 'MN', 'name' => 'Minnesota'),
            array('code' => 'US', 'abbreviation' => 'MS', 'name' => 'Mississippi'),
            array('code' => 'US', 'abbreviation' => 'MO', 'name' => 'Missouri'),
            array('code' => 'US', 'abbreviation' => 'MT', 'name' => 'Montana'),
            array('code' => 'US', 'abbreviation' => 'NE', 'name' => 'Nebraska'),
            array('code' => 'US', 'abbreviation' => 'NV', 'name' => 'Nevada'),
            array('code' => 'US', 'abbreviation' => 'NH', 'name' => 'New Hampshire'),
            array('code' => 'US', 'abbreviation' => 'NJ', 'name' => 'New Jersey'),
            array('code' => 'US', 'abbreviation' => 'NM', 'name' => 'New Mexico'),
            array('code' => 'US', 'abbreviation' => 'NY', 'name' => 'New York'),
            array('code' => 'US', 'abbreviation' => 'NC', 'name' => 'NC'),
            array('code' => 'US', 'abbreviation' => 'ND', 'name' => 'North Dakota'),
            array('code' => 'US', 'abbreviation' => 'OH', 'name' => 'Ohio'),
            array('code' => 'US', 'abbreviation' => 'OK', 'name' => 'Oklahoma'),
            array('code' => 'US', 'abbreviation' => 'OR', 'name' => 'Oregon'),
            array('code' => 'US', 'abbreviation' => 'PA', 'name' => 'Pennsylvania'),
            array('code' => 'US', 'abbreviation' => 'RI', 'name' => 'Rhode Island'),
            array('code' => 'US', 'abbreviation' => 'SC', 'name' => 'South Carolina'),
            array('code' => 'US', 'abbreviation' => 'SD', 'name' => 'South Dakota'),
            array('code' => 'US', 'abbreviation' => 'TN', 'name' => 'Tennessee'),
            array('code' => 'US', 'abbreviation' => 'TX', 'name' => 'Texas'),
            array('code' => 'US', 'abbreviation' => 'UT', 'name' => 'Utah'),
            array('code' => 'US', 'abbreviation' => 'VT', 'name' => 'Vermont'),
            array('code' => 'US', 'abbreviation' => 'VA', 'name' => 'Virginia'),
            array('code' => 'US', 'abbreviation' => 'WA', 'name' => 'Washington'),
            array('code' => 'US', 'abbreviation' => 'WV', 'name' => 'West Virginia'),
            array('code' => 'US', 'abbreviation' => 'WI', 'name' => 'Wisconsin'),
            array('code' => 'US', 'abbreviation' => 'WY', 'name' => 'Wyoming'),
            array('code' => 'US', 'abbreviation' => 'AS', 'name' => 'American Samoa'),
            array('code' => 'US', 'abbreviation' => 'DC', 'name' => 'District of Columbia'),
            array('code' => 'US', 'abbreviation' => 'GU', 'name' => 'Guam'),
            array('code' => 'US', 'abbreviation' => 'MP', 'name' => 'Northern Mariana Islands'),
            array('code' => 'US', 'abbreviation' => 'PR', 'name' => 'Puerto Rico'),
            array('code' => 'US', 'abbreviation' => 'VI', 'name' => 'United States Virgin Islands'),
        );
    }

    public function InputType()
    {
        return [
            array('name' => 'tel', 'mask' => ''),
            array('name' => 'email', 'mask' => ''),
            array('name' => 'city', 'mask' => ''),
            array('name' => 'custom', 'mask' => ''),
            array('name' => 'checkbox', 'mask' => ''),
            array('name' => 'color', 'mask' => ''),
            array('name' => 'date', 'mask' => ''),
            array('name' => 'datetime-local', 'mask' => ''),
            array('name' => 'file', 'mask' => ''),
            array('name' => 'hidden', 'mask' => ''),
            array('name' => 'image', 'mask' => ''),
            array('name' => 'map', 'mask' => ''),
            array('name' => 'month', 'mask' => ''),
            array('name' => 'number', 'mask' => ''),
            array('name' => 'password', 'mask' => ''),
            array('name' => 'radio', 'mask' => ''),
            array('name' => 'range', 'mask' => ''),
            array('name' => 'select', 'mask' => ''),
            array('name' => 'select_mulitple', 'mask' => ''),
            array('name' => 'tel', 'mask' => ''),
            array('name' => 'text', 'mask' => ''),
            array('name' => 'time', 'mask' => ''),
            array('name' => 'url', 'mask' => ''),
            array('name' => 'week', 'mask' => ''),
            array('name' => 'textarea', 'mask' => ''),
            array('name' => 'video', 'mask' => ''),
            array('name' => 'city', 'mask' => ''),
            array('name' => 'zip', 'mask' => ''),
            array('name' => 'address', 'mask' => ''),
            array('name' => 'related', 'mask' => ''),
        ];
    }

}
