<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;

class ModuleSubpanel extends Model
{
    protected $table = 'ice_module_subpanels';

    use HasFactory;

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }

    public function subpanelfields()
    {
        return $this->hasMany(SubpanelField::class, 'subpanel_id', 'id')->with('field');
    }

    public static function getSubpanels($moduleId, $id)
    {
        $returnArray = [];
        $subPanels = self::where('module_id', $moduleId)->with('relationship')->with('module')->get();
        foreach ($subPanels as $subPanel) {
            $data = [];
            $modules = explode(',', $subPanel->relationship->modules);
            $subPanelModule = Module::find($subPanel->module_id)->firstOrFail();
            if (count($modules) > 0) {
                $relationshipQuery = DB::table($subPanel->relationship->name);

                $table_primary_ids = '';
                foreach ($modules as $module_id) {
                    $joinModule = Module::where('id', $module_id)->first();
                    $relationshipQuery->join($joinModule->name, $subPanel->relationship->name.'.'.$joinModule->name.'_id', '=', $joinModule->name.'.id');
                    $table_primary_ids .= ', '.$joinModule->name.'.id as '.$joinModule->name.'_row_id';
                }
                $data = $relationshipQuery->selectRaw($subPanel->subpanel_fields.', '
                    .$subPanel->relationship->name.'.'.'id as row_id'
                    .$table_primary_ids)
                    ->where($subPanelModule->name.'_id', $id)
                    ->orderBy($subPanel->relationship->name.'.'.$subPanel->list_order_column, $subPanel->list_order)
                    ->take($subPanel->list_size)
                    ->get();
            }

            $subPanelFields = [];
            $subPanelFieldData = explode(',', $subPanel->subpanel_fields);

            foreach ($subPanelFieldData as $fields) {
                $data_part = explode(' as ', $fields);
                if (isset($data_part[1])) {
                    $field = explode('__', $data_part[1]);
                }
                $module = Module::where('name', strtolower(ucfirst($field[0])))->first();
                if ($module) {
                    $subPanelFields[] = Field::where('name', $field[1])
                        ->where('module_id', $module->id)
                        ->with('module')
                        ->with('related_module')
                        ->first();
                }
            }

            $field_names = [];
            foreach (explode(',', $subPanel->subpanel_fields) as $value) {
                $data_part = explode(' as ', $value);
                if (isset($data_part[1])) {
                    //$field_names[$data_part[1]]=$data_part[0];
                    $field_names[] = $data_part[1];
                }
            }

            $returnArray[] = [
                'id' => $subPanel->id,
                'name' => $subPanel->name,
                'label' => $subPanel->label,
                'field_labels' => $field_names,
                'fields' => $subPanelFields,
                'data' => $data,
            ];
        }

        return $returnArray;
    }

    public static function getSubpanelData($id, $request)
    {
        $subPanel = ModuleSubPanel::where('id', $id)->with('subpanelfields')->first();
        $data = [];

        $options = [];
        $options['relationship_id'] = $subPanel->relationship_id;
        $options['search_type'] = 'relationship';

        if (isset($request['per_page'])) {
        $options['per_page'] = $request['per_page'];
        } else {
        $options['per_page'] = Setting::getSetting('submodule_search_per_page');
        }

        if (isset($request['search_field']) && isset($request['search_text']) && strlen($request['search_text']) > 2) {
            $options[$request['search_field']] = $request['search_text'];
        }

        return [
            'id' => $subPanel->id,
            'name' => $subPanel->name,
            'label' => $subPanel->label,
            'relationship_id' => $subPanel->relationship_id,
            'fields' => $subPanel->subpanelfields->toArray(),
            'data' => Search::getData($options)->toArray(),
        ];
    }

    public static function processRecords($subpanelId, $selectedRecords, $newRecords, $record_id = 0)
    {
        $subpanel = ModuleSubpanel::where('id', $subpanelId)
            ->with('relationship.relationshipmodule.module.fields.module')
            ->firstOrFail();

        foreach ($subpanel->relationship->relationshipmodule as $relationshipModule) {
            if (! empty($relationshipModule->module->id)) {
                if (! empty($newRecords[$relationshipModule->module->id])) {
                    $newRecords[$relationshipModule->module->id]['from_id'] = $newRecords['from_id'];
                    $newRecords[$relationshipModule->module->id]['from_module'] = $newRecords['from_module'];

                    $selectedRecords[$relationshipModule->module->id] = Module::saveRecord($relationshipModule->module->id, $newRecords[$relationshipModule->module->id]);
                }
            }
        }

        return Relationship::saveRecord($subpanel->relationship_id, $selectedRecords, $record_id);
    }

    public static function selectFieldToDatabaseField($fields)
    {
        $fieldsArray = [];
        $fieldsData = explode(',', $fields);
        foreach ($fieldsData as $field) {
            $field = explode('.', $field);
            $module = Module::where('name', strtolower(ucfirst($field[0])))->first();
            if ($module) {
                $item = Field::where('name', $field[1])
                    ->where('module_id', $module->id)
                    ->with('module')
                    ->with('related_module')
                    ->first();

                if ($item) {
                    $fieldsArray[] = (object) $item;
                }

            }
        }

        return $fieldsArray;
    }

    public static function parseRequest($request)
    {
        $data = $request->all();
        $selectedRecords = [];
        $newRecords = [];
        $subpanelId = 0;
        $recordId = 0;
        foreach ($data as $key => $value) {
            if (str_contains($key, '__')) {
                $parts = explode('__', $key);
                $newRecords[$parts[0]][$key] = $value;
            } elseif ($key == 'module_records') {
                $selectedRecords = $value;
            } elseif (str_contains($key, 'subpanel_id')) {
                $subpanelId = $value;
            } elseif (str_contains($key, 'record_id')) {
                $recordId = $value;
            } elseif (str_contains($key, 'from_id')) {
                $newRecords[$key] = $value;
            } elseif (str_contains($key, 'from_module')) {
                $newRecords[$key] = $value;
            }
        }

        return [$subpanelId, $selectedRecords, $newRecords, $recordId];
    }
}
