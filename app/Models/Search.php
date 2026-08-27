<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;

class Search extends Model
{
    use HasFactory;

    public static $excludeFieldTypes = [
        'Search' => ['password', 'file', 'image', 'audio', 'video'],
        'OrderBy' => ['password', 'image', 'audio', 'video'],
        'Display' => ['password'],
        'All' => [],
    ];

    public static function getData($request, $replaceIds = false)
    {
        $request = self::initializeSearch($request);
        self::authorizeSearch($request);

        if ($request['search_type'] == 'relationship') {
            [$results, $order_by_field] = self::relationshipSearch($request);

        } else {
            [$results, $order_by_field] = self::ModuleSearch($request);
        }

        $filterFields = self::filterFields($request);
        foreach (self::searchFilters($request) as $filter) {
            $field = $filterFields->get($filter['module_id'].'__'.$filter['name']);

            if (! $field) {
                abort(404);
            }

            $value = $filter['value'];
            $operator = $field->data_type == 'string' ? 'LIKE' : '=';
            if ($field->input_type == 'checkbox') {
                $value = $value == 'true' ? 1 : 0;
            }

            $results->where($field->module->name.'.'.$field->name, $operator, $value);
        }
        if (! isset($request['order_by']) || empty($request['order_by'])) {
            $request['order_by'] = $order_by_field;
        }

        $pieces = explode('__', $request['order_by']);
        if (isset($pieces[1]) && $pieces[0] && is_numeric($pieces[0])) {
            $moduleName = $filterFields
                ->firstWhere('module_id', intval($pieces[0]))
                ?->module
                ?->name;

            if (! $moduleName) {
                $moduleName = Module::where('id', intval($pieces[0]))->value('name');
            }

            $request['order_by'] = $moduleName.'.'.$pieces[1];
        }

        return $results
            ->orderBy($request['order_by'], $request['search_order'])
            ->paginate($request['per_page']);

    }

    private static function searchFilters($request): array
    {
        $filters = [];

        foreach ($request as $key => $value) {
            $pieces = explode('__', $key);

            if (isset($pieces[1]) && intval($pieces[0]) > 0 && ($value != '' && $value != 'undefined')) {
                $filters[] = [
                    'module_id' => intval($pieces[0]),
                    'name' => $pieces[1],
                    'value' => $value,
                ];
            }
        }

        return $filters;
    }

    private static function filterFields($request)
    {
        $filters = self::searchFilters($request);

        if (empty($filters)) {
            return collect();
        }

        return Field::whereIn('module_id', array_unique(array_column($filters, 'module_id')))
            ->whereIn('name', array_unique(array_column($filters, 'name')))
            ->with('module')
            ->get()
            ->keyBy(fn ($field) => $field->module_id.'__'.$field->name);
    }

    protected static function authorizeSearch($request): void
    {
        if ($request['search_type'] == 'relationship') {
            if (isset($request['relationship_name']) && strlen($request['relationship_name']) > 0) {
                $relationship = Relationship::where('name', $request['relationship_name'])->firstOrFail();
            } else {
                $relationship = Relationship::where('id', $request['relationship_id'] ?? 0)->firstOrFail();
            }

            $moduleIds = RelationshipModule::where('relationship_id', $relationship->id)->pluck('module_id');
            foreach ($moduleIds as $moduleId) {
                if (! Permission::checkPermission($moduleId, 'read')) {
                    abort(403, 'No Access');
                }
            }

            return;
        }

        if (! Permission::checkPermission((int) ($request['module_id'] ?? 0), 'read')) {
            abort(403, 'No Access');
        }
    }

    public static function initializeSearch($request)
    {
        if (empty($request['page']) || intval($request['page']) < 1) {
        $request['page'] = 1;
        }
        if (empty($request['per_page']) || intval($request['per_page']) < 1) {
        $request['per_page'] = Setting::getSetting('search_per_page');
        }
        if (empty($request['search_type']) || $request['search_type'] == '') {
        $request['search_type'] = 'module';
        }
        if (empty($request['search_order'])) {
        $request['search_order'] = 'asc';
        }
        if (empty($request['text_search_type'])) {
        $request['text_search_type'] = 'exact';
        }

        return $request;
    }

    public static function relationshipSearch($request)
    {
        $selectFields = [];
        $order_by_field = '';

        if (isset($request['relationship_name']) && strlen($request['relationship_name']) > 0) {
            $relationship = Relationship::where('name', $request['relationship_name'])->firstOrFail();
        } else {
            $relationship = Relationship::where('id', $request['relationship_id'])->firstOrFail();
        }
        $modules = RelationshipModule::where('relationship_id', $relationship->id)
            ->with('module.fields')
            ->get();
        $results = DB::table($relationship->name);

        $table_primary_ids = '';
        foreach ($modules as $relationshipModule) {
            $joinModule = $relationshipModule->module;

            foreach ($joinModule->fields as $field) {
                $selectFields[] = $joinModule->name.'.'.$field->name.' as '.$joinModule->name.'__'.$field->name;
            }

            $results->join($joinModule->name, $relationship->name.'.'.$joinModule->name.'_id', '=', $joinModule->name.'.id');
            $table_primary_ids .= ', '.$joinModule->name.'.id as '.$joinModule->name.'_row_id';

            $order_by_field = $joinModule->name.'_row_id';
        }
        $selectStatement = implode(',', $selectFields);
        $selectStatement = $selectStatement.$table_primary_ids.', '.$relationship->name.'.id as relationship_id';
        $results->selectRaw($selectStatement);

        return [$results, $order_by_field];
    }

    public static function moduleSearch($request)
    {
        $selectFields = [];
        $selectStatement = '';

        $module = Module::where('id', intval($request['module_id']))->firstOrFail();

        if (! isset($request['order_by']) || $request['order_by'] == '') {
            $request['order_by'] = $module->primary_field;
        }
        $fields = Field::where('module_id', $module->id)->get();
        foreach ($fields as $field) {
            if (empty($request['typeahead']) || (in_array($field->input_type, ['text', 'tel', 'email']))) {
                $selectFields[] = $module->name.'.'.$field->name.' as '.$module->name.'__'.$field->name;
            }
        }

        if (isset($request['typeahead'])) {
            $selectStatement = $module->primary_field. ', ';
        }
        $selectStatement .= implode(',', $selectFields);
        if ($selectStatement != '') {
        $selectStatement .= ', ';
        }
        $results = DB::table($module->name)
            ->selectRaw($selectStatement.$module->name.'.' . $module->primary_field . ' as '.$module->name.'_row_id');

        $order_by_field = $module->name.'_row_id';

        return [$results, $order_by_field];
    }

    public static function getFields($id, $type = '', $fieldType = 'All'): array
    {

        $modules = [];
        if ($type == 'relationship') {
            $relationship = Relationship::where('id', $id)->with('relationshipmodule')->first();
            foreach ($relationship->relationshipmodule as $relationshipmodule) {
                $modules[] = $relationshipmodule->module_id;
            }
        } else {
            $modules[] = $id;
        }

        $fields = [];
        $query = Field::whereIn('module_id', $modules)
            ->whereNotIn('input_type', self::$excludeFieldTypes[$fieldType])
            ->with('module')
            ->with('related_module');

        if($fieldType == "Search")
        {
            $query->where('search_display', 1)
                ->orderBy('search_order')
                ->orderBy('id');
        }
        elseif($fieldType == "List")
        {
            $query->where('list_display', 1)
                ->orderBy('display_order')
                ->orderBy('id');
        }
        elseif($fieldType == "Display")
        {
            $query->where('list_display', 1)
                ->orderBy('display_order')
                ->orderBy('id');
        }
        elseif($fieldType == "Edit")
        {
            $query->where('edit_display', 1)
                ->orderBy('edit_order')
                ->orderBy('id');
        }
        $field_collection = $query->get();

        $field_collection->each(function ($field) use (&$fields) {

            $fields[$field->id.'__'.$field->name] = $field;
        });

        return $fields;

    }
}
