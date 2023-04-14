<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datalet extends Model
{
    use HasFactory;

    protected $table = 'ice_datalets';

    public function type()
    {
        return $this->hasOne(DataletType::class, 'id', 'type');
    }

    public function module()
    {
        return $this->hasOne(Module::class, 'id', 'module_id');
    }

    public function field()
    {
        return $this->hasOne(Field::class, 'id', 'field_id');
    }

    public function relationship()
    {
        return $this->hasOne(Relationship::class, 'id', 'relationship_id');
    }

    public static function getDataAllActiveData()
    {
        $data = [];

        foreach (Datalet::where('role_id', 0)->orWhere('role_id', Auth::user()->role_id)
                    ->with('type')
                    ->with('module')
                    ->with('field')
                    ->with('relationship')
                    ->orderBy('display_order')
                    ->get()
                as $datalet) {
            $data[] =
                [
                    'datalet' => $datalet,
                    'data' => $datalet->getData(),
                ];
            // data' => Datalet::getData($datalet->id)
        }

        return $data;
    }

    public function getData()
    {
        $datalet = $this;
        $returnData = [];

           // $datalet=Datalet::where('id', $id)->first();
            switch ($datalet->type) {
                case 1:
                    $returnData = [
                        'labels' => ['Tax', 'Discount', 'Gross', 'Net'],
                        'data' => [
                            round(DB::table('lineitems')->sum('taxes') / 10, 2),
                            round(DB::table('lineitems')->sum('discount') / 5, 2),
                            DB::table('lineitems')->sum('gross'),
                            round(DB::table('lineitems')->sum('gross') / 2, 2),
                        ],
                    ];
                    break;
                case 2:
                    $returnData = [
                        'labels' => ['Leads', 'Contacts', 'Accounts'],
                        'data' => [
                            DB::table('leads')
                                ->where('created_at', '>', Carbon::now()->subDays(7)->toDateTimeString())
                                ->count('id'),
                            DB::table('contacts')
                                ->where('created_at', '>', Carbon::now()->subDays(7)->toDateTimeString())
                                ->count('id'),
                            DB::table('accounts')
                                ->where('created_at', '>', Carbon::now()->subDays(7)->toDateTimeString())
                                ->count('id'),

                        ],
                    ];
                    break;
                case 3:
                    $returnData = [
                        'labels' => ['Today', 'Last 7 Days', 'Last 30 Days'],
                        'data' => [
                            DB::table('meetings')
                                ->where('created_at', '>', Carbon::now()->subDays(1)->toDateTimeString())
                                ->count('id'),
                            DB::table('meetings')
                                ->where('created_at', '>', Carbon::now()->subDays(7)->toDateTimeString())
                                ->count('id'),
                            DB::table('meetings')
                                ->where('created_at', '>', Carbon::now()->subDays(30)->toDateTimeString())
                                ->count('id'),
                        ],
                    ];
                    break;
                case 4:
                    $returnData = [
                        'labels' => ['Opportunities', 'Quotes', 'Contracts'],
                        'data' => [
                            DB::table('opportunities')->count('id'),
                            DB::table('quotes')->count('id'),
                            DB::table('contracts')->count('id'),

                        ],
                    ];
                    break;
                case 5:
                    $returnData = [
                        'labels' => ['Tax', 'Discount', 'Gross', 'Net'],
                        'data' => [
                            DB::table('invoices')
                                ->where('created_at', '>', Carbon::now()->subDays(30)->toDateTimeString())
                                ->sum('tax'),
                            DB::table('invoices')
                                ->where('created_at', '>', Carbon::now()->subDays(30)->toDateTimeString())
                                ->sum('discount'),
                            DB::table('invoices')
                                ->where('created_at', '>', Carbon::now()->subDays(30)->toDateTimeString())
                                ->sum('subtotal'),
                            DB::table('invoices')
                                ->where('created_at', '>', Carbon::now()->subDays(30)->toDateTimeString())
                                ->sum('total'),
                        ],
                    ];
                    break;
                case 6:
                    $meeting = DB::table('meetings')
                        ->where('status', '>', 0)
                        ->orderBy('updated_at', 'desc')
                        ->first();
                    $meeting->type = DB::table('meeting_types')->where('id', $meeting->types)->value('name');
                    $returnData = [$meeting];
                    break;
                case 7:
                    $returnData = [
                        'modules' => Module::all()->count(),
                        'fields' => Field::all()->count(),
                        'subpanels' => ModuleSubpanel::all()->count(),
                        'relationships' => Relationship::all()->count(),
                    ];
                    break;
                case 8:
                    $returnData = [
                        [
                            'name' => ucfirst(Module::where('id', 1)->value('name')),
                            'value' => DB::table(Module::where('id', 1)->value('name'))
                            ->count(),
                            'class' => 'success',
                        ],
                        ['name' => ucfirst(Module::where('id', 2)->value('name')),
                            'value' => DB::table(Module::where('id', 2)->value('name'))
                                ->count(),
                            'class' => 'primary'],
                        ['name' => ucfirst(Module::where('id', 3)->value('name')),
                            'value' => DB::table(Module::where('id', 3)->value('name'))
                                ->count(),
                            'class' => 'secondary'],
                        ['name' => ucfirst(Module::where('id', 4)->value('name')),
                            'value' => DB::table(Module::where('id', 4)->value('name'))
                                ->count(),
                            'class' => 'accident'],
                        ['name' => ucfirst(Module::where('id', 5)->value('name')),
                            'value' => DB::table(Module::where('id', 5)->value('name'))
                                ->count(),
                            'class' => 'warning'],
                    ];
                    break;
                default:
                    break;
            }
       // dd($returnData);
        return $returnData;
    }
}
