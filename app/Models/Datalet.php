<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Search;
use App\Models\Field;
use Auth;
use DB;

class Datalet extends Model
{
    use HasFactory;

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

    public static function getData($id=0)
    {
        $returnData=[];
        if(intval($id) > 0)
        {
            $datalet=Datalet::where('id', $id)->first();
            switch($datalet->id)
            {
                case 1:
                    $returnData = [
                        'labels' => ['Tax', 'Discount', 'Gross', 'Net'],
                        'data' => [
                            round(DB::table('lineitems')->sum('taxes')/10, 2),
                            round(DB::table('lineitems')->sum('discount')/5, 2),
                            DB::table('lineitems')->sum('gross'),
                            round(DB::table('lineitems')->sum('gross')/2, 2),
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
                default:
                    break;
            }
        }
        return $returnData;
    }
}
