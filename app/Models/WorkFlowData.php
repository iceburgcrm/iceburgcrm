<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkFlowData extends Model
{
    use HasFactory;

    protected $table = 'ice_work_flow_data';

    public function from_step()
    {
        return $this->hasOne(ModuleConvertable::class, 'primary_module_id', 'from_module_id');
    }

    public function to_step()
    {
        return $this->hasOne(ModuleConvertable::class, 'module_id', 'to_module_id');
    }

    public function to_step_primary()
    {
        return $this->hasOne(ModuleConvertable::class, 'primary_module_id', 'to_module_id');
    }

    public function from_step_primary()
    {
        return $this->hasOne(ModuleConvertable::class, 'module_id', 'from_module_id');
    }

    public static function get360Data($module_id, $record_id)
    {
        $data = [];
        $current = self::getRecord($module_id, $record_id);
        if ($current) {
            $record = $current;
            $data[$record->to_module_id] = [
                'module_name' => $record->to_step_primary->primary_module->name,
                'module_label' => $record->to_step_primary->primary_module->label,
                'link_id' => $record->to_id,
                'current' => true,
            ];

            $original_module_id = $module_id;
            $original_record_id = $record_id;

            $module_id = $record->from_module_id;
            $record_id = $record->from_id;

            while ($record = self::getRecord($module_id, $record_id)) {

                $module_id = $record->from_module_id;
                $record_id = $record->from_id;

                    $data[$record->to_module_id] = [
                        'module_name' => $record->to_step_primary->primary_module->name,
                        'module_label' => $record->to_step_primary->primary_module->label,
                        'link_id' => $record->to_id,
                    ];
            }

                $module_id = $original_module_id;
                $record_id = $original_record_id;

                while ($record = self::getFutureRecord($module_id, $record_id)) {

                    $module_id = $record->to_module_id;
                    $record_id = $record->to_id;

                    if ($record->to_module_id) {
                        $data[$record->to_module_id] = [
                            'module_name' => $record->from_step->module->name,
                            'module_label' => $record->from_step->module->label,
                            'link_id' => $record->to_id,
                        ];
                    }

                }
        }

        $output = [];
        if (count($data) > 0) {

            $steps = ModuleConvertable::where('id', '>', 0)
                ->with('primary_module')
                ->orderBy('level')
                ->get();
            foreach ($steps as $step) {
                $step->current = false;
                if (isset($data[$step->primary_module_id])) {
                    $step->className = 'step step-primary';
                    if (property_exists((object) $data[$step->primary_module_id], 'current')) {
                        $step->current = true;
                    }
                    $step->from_data = $data[$step->primary_module_id];
                } else {
                    $step->className = 'step';
                }
                $output[] = $step;
            }
        }

        return $output;
    }

    public static function getRecord($module_id, $record_id)
    {
        $data = [];
        $mc = ModuleConvertable::where('primary_module_id', $module_id)->value('id');
        if (intval($mc) > 0) {
            $data = WorkFlowData::where('to_id', $record_id)
                ->where('to_module_id', $module_id)
                ->with('from_step.module')
                ->with('from_step_primary.primary_module')
                ->with('to_step.module')
                ->with('to_step_primary.primary_module')
                ->first();
        }

        return $data;
    }

    public static function getFutureRecord($module_id, $record_id)
    {
        return WorkFlowData::where('from_id', $record_id)
            ->where('from_module_id', $module_id)
            ->with('from_step.primary_module')
            ->with('to_step.module')
            ->first();
    }
}
