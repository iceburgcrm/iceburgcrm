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
            $subPanelModule = $subPanel->module;

            if (count($modules) > 0) {
                $joinModules = Module::whereIn('id', $modules)->get()->keyBy('id');
                $relationshipQuery = DB::table($subPanel->relationship->name);

                $table_primary_ids = '';
                foreach ($modules as $module_id) {
                    $joinModule = $joinModules->get((int) $module_id);
                    if (! $joinModule) {
                        continue;
                    }

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

            $subPanelFields = self::selectAliasesToFields($subPanel->subpanel_fields);

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
        return self::fieldReferencesToFields(
            collect(explode(',', $fields))
                ->map(function ($field) {
                    $parts = explode('.', trim($field));

                    return count($parts) === 2 ? $parts : null;
                })
                ->filter()
                ->all()
        );
    }

    private static function selectAliasesToFields(string $fields): array
    {
        $references = collect(explode(',', $fields))
            ->map(function ($field) {
                $dataPart = explode(' as ', trim($field));
                if (! isset($dataPart[1])) {
                    return null;
                }

                $parts = explode('__', trim($dataPart[1]));

                return count($parts) === 2 ? $parts : null;
            })
            ->filter()
            ->all();

        return self::fieldReferencesToFields($references);
    }

    private static function fieldReferencesToFields(array $references): array
    {
        if (empty($references)) {
            return [];
        }

        $moduleNames = collect($references)
            ->map(fn ($reference) => strtolower($reference[0]))
            ->unique()
            ->values();

        $modules = Module::whereIn('name', $moduleNames)->get()->keyBy('name');
        $fieldNames = collect($references)->map(fn ($reference) => $reference[1])->unique()->values();
        $fields = Field::whereIn('module_id', $modules->pluck('id'))
            ->whereIn('name', $fieldNames)
            ->with('module')
            ->with('related_module')
            ->get()
            ->keyBy(fn ($field) => $field->module->name.'.'.$field->name);

        return collect($references)
            ->map(fn ($reference) => $fields->get(strtolower($reference[0]).'.'.$reference[1]))
            ->filter()
            ->values()
            ->all();
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
