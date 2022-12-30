<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;

class Search extends Model
{
    use HasFactory;

    private static $excludeFieldTypes = [
        'Search' => ['password', 'file', 'image', 'audio', 'video'],
        'OrderBy' => ['password', 'image', 'audio', 'video'],
        'Display' => ['password'],
        'All' => []
    ];


    public static function getData($request, $replaceIds = false)
    {

        $request = self::initializeSearch($request);

        if ($request['search_type'] == "relationship") {
            [$results, $order_by_field] = self::relationshipSearch($request);

        } else {
            [$results, $order_by_field] = self::ModuleSearch($request);
        }

        foreach ($request as $key => $value) {
            $pieces = explode('__', $key);
            if (isset($pieces[0]) && intval($pieces[0]) > 0 && ($value != '' && $value != 'undefined')) {
                $field = Field::where('module_id', intval($pieces[0]))
                    ->where('name', 'like', $pieces[1])
                    ->with('module')
                    ->firstOrFail();
                $operator = '=';
                if ($field->data_type == 'string') {
                    $operator = 'LIKE';
                }
                $results->where($field->module->name . "." . $pieces[1], $operator, $value);
            }
        }

        if (!isset($request['order_by']) || empty($request['order_by'])) {
            $request['order_by'] = $order_by_field;
        }

        $pieces = explode("__", $request['order_by']);
        if (isset($pieces[1]) && $pieces[0] && is_numeric($pieces[0])) {
            $moduleOrderBy = Module::where('id', intval($pieces[0]))->first();
            $request['order_by'] = $moduleOrderBy->name . "." . $pieces[1];
        }
        return $results
            ->orderBy($request['order_by'], $request['search_order'])
            ->paginate($request['per_page']);

    }

    public static function initializeSearch($request)
    {
        if (empty($request['page']) || intval($request['page']) < 1) $request['page'] = 1;
        if (empty($request['per_page']) || intval($request['per_page']) < 1) $request['per_page'] = Setting::getSetting('search_per_page');
        if (empty($request['search_type']) || $request['search_type'] == '') $request['search_type'] = 'module';
        if (empty($request['search_order'])) $request['search_order'] = 'asc';
        if (empty($request['text_search_type'])) $request['text_search_type'] = 'exact';

        return $request;
    }

    public static function relationshipSearch($request)
    {
        $selectFields = [];
        $order_by_field = '';

        if(isset($request['relationship_name']) && strlen($request['relationship_name']) > 0)
        {
            $relationship=Relationship::where('name', $request['relationship_name'])->firstOrFail();
        }
        else
        {
            $relationship=Relationship::where('id', $request['relationship_id'])->firstOrFail();
        }
        $modules=RelationshipModule::where('relationship_id', $relationship->id)->pluck('module_id');
        $results=DB::table($relationship->name);

        $table_primary_ids='';
        foreach($modules as $module_id){
            $joinModule=Module::where('id', $module_id)->firstOrFail();
            $fields = Field::where('module_id', $joinModule->id)->with('module')->with('related_module')->get();
            foreach ($fields as $field) {
                $selectFields[] = $joinModule->name . '.' . $field->name . ' as ' . $joinModule->name . "__" . $field->name;
            }

            $results->join($joinModule->name, $relationship->name. '.' . $joinModule->name . '_id', '=', $joinModule->name . '.id');
            $table_primary_ids.= ", " . $joinModule->name . '.id as ' . $joinModule->name . '_row_id';

            $order_by_field = $joinModule->name . '_row_id';
        }
        $selectStatement=implode(',', $selectFields);
        $selectStatement= $selectStatement . $table_primary_ids . ', ' . $relationship->name . '.id as relationship_id';
        $results->selectRaw($selectStatement);

        return [$results, $order_by_field];
    }

    public static function moduleSearch($request)
    {
        $selectFields = [];
        $selectStatement = '';

        if (!isset($request['order_by']) || $request['order_by'] == '') $request['order_by'] = 'id';
        $module = Module::where('id', intval($request['module_id']))->firstOrFail();

        $fields = Field::where('module_id', $module->id)->with('module')->with('related_module')->get();
        foreach ($fields as $field) {
            if (empty($request['typeahead']) || (in_array($field->input_type, ["text", "tel", "email"]))) {
                $selectFields[] = $module->name . '.' . $field->name . ' as ' . $module->name . "__" . $field->name;
            }
        }

        if (isset($request['typeahead'])) $selectStatement = "id, ";
        $selectStatement .= implode(',', $selectFields);
        if ($selectStatement != '') $selectStatement .= ', ';
        $results = DB::table($module->name)
            ->selectRaw($selectStatement . $module->name . '.' . "id as " . $module->name . "_row_id");

        $order_by_field = $module->name . '_row_id';

        return [$results, $order_by_field];
    }


    public static function getFields($id, $type = '', $fieldType = 'All') : array
    {

        $modules=[];
        if($type == "relationship")
        {
            $relationship=Relationship::where('id', $id)->with('relationshipmodule')->first();
            foreach($relationship->relationshipmodule as $relationshipmodule)
            {
                $modules[]=$relationshipmodule->module_id;
            }
        }
        else
        {
            $modules[]=$id;
        }

        $fields=[];
        $field_collection=Field::whereIn('module_id', $modules)
            ->whereNotIn('input_type', self::$excludeFieldTypes[$fieldType])
            ->with('module')
            ->with('related_module')
            ->get();

        $field_collection->each(function($field) use (&$fields) {

            $fields[$field->id . "__" . $field->name]=$field;
        });
        return $fields;

    }
}
