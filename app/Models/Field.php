<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Log;

class   Field extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function generate(){
        $this->where('status', 1)->get()->each(function ($module) {
            $this->generateTable($module);
        });
        Log::info("generateTable done");
    }

    public static function generateTableData($module){
        $data=json_decode($dataList->data);
        foreach($data as $item){
            DB::table($dataList->name)->insert((array) $item);
        }
        Log::info("generate " . $dataList->name  . " Data");
    }

    public static function getField(Array $data, $order=0)
    {
        $defaultData = [
            'name'          => '',
            'label'         => '',
            'module_id'     => 0,
            'input_type'    => 'text',
            'data_type'     => 'string',
            'field_length'  => 245,
            'is_nullable'   => 1,
            'default_value' => '',
            'read_only'     => 0,
            'related_field_id' => 0,
            'related_value_id'  => 0,
            'decimal_places' => null,
        ];

        foreach($data as $key => $value)
        {
            $defaultData[$key] = $value;
        }
        return $defaultData;
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }

    public function related_module()
    {
        return $this->hasOne('App\Models\Module', 'id', 'related_module_id');
    }

    public static function getSelectFields($module)
    {
        $selectFields=[];
        $fields = Field::where('module_id', $joinModule->id)->with('module')->with('related_module')->get();
        foreach ($fields as $field) {
            $selectFields[] = $joinModule->name . '.' . $field->name . ' as ' . $joinModule->name . "__" . $field->name;
        }
        return $selectFields;
    }

    public static function getRelatedFieldData($module_id)
    {
        $output=[];
        $fields = Field::where('module_id', $module_id)->where('related_module_id', '>', 0)->where('status', 1)->get();
        foreach ($fields as $field){

            $relatedModule=Module::find($field->related_module_id);
            $output[$field->name]=DB::table($relatedModule->name)->get()->toArray();

        }
        return $output;
    }

}
