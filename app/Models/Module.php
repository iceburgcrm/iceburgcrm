<?php

namespace App\Models;

use App\Events\ModuleRecordDeleted;
use App\Seeder\FieldSeeder;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema as Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Module extends Model
{
    use HasFactory;

    protected $table = 'ice_modules';

    public static function getId(string $name)
    {
        return self::where('name', 'like', strtolower($name))
            ->pluck('id')
            ->first();
    }

    public function groups()
    {
        return $this->belongsTo(ModuleGroup::class);
    }

    public function fields()
    {
        return $this->hasMany('App\Models\Field', 'module_id', 'id')
            ->where('status', 1);
    }

    public static function menu()
    {
        return self::where('status', 1)->get();
    }

    public function convertedmodules()
    {
        return $this->hasOne(ModuleConvertable::class, 'primary_module_id', 'id')
            ->with('module');
    }

    public function subpanels()
    {
        return $this->hasMany(ModuleSubpanel::class, 'module_id', 'id');
    }

    public function generate($seed = 0, $moduleId = 0)
    {
        $this->where('status', 1)
            ->where('create_table', 1)
            ->get()
            ->each(function ($module) use ($seed, $moduleId) {
                if ($moduleId == 0
                    ||
                    (intval($moduleId) > 0 && $moduleId == $module->id)) {
                    Log::info('Generating module: '.$module->name);
                    if (Schema::hasTable($module->name)) {
                        Schema::drop($module->name);
                    }

                    Schema::create($module->name, function ($table) use ($module) {
                        $module->fields()->get()->each(function ($field) use ($table) {
                            if ($field->input_type == 'checkbox') {
                                $field->is_nullable = false;
                                $field->default_value = 0;
                            }
                            if ($field->is_nullable) {
                                $table->{$field->data_type}($field->name, $field->length, $field->decimal_places)
                                ->nullable($field->is_nullable);
                            } else {
                                $table->{$field->data_type}($field->name, $field->length, $field->decimal_places)
                                    ->default($field->default_value);
                            }
                        });
                        $table->id();
                        $table->string('slug', 64)->unique();
                        $table->integer('soft_delete')->default(0);
                        $table->timestamps();
                    });

                    if ($seed > 0 && $module->faker_seed == 1) {
                        $fieldSeeder = new FieldSeeder($module);
                        $fieldSeeder->seed($seed);

                    }
                }
            });

        return true;
    }

    public static function getRelatedModuleList($moduleId, $related_field_id, $related_value_id)
    {
        $module = Module::findOrFail($moduleId);

        return DB::table($module->name)
            ->selectRaw($related_field_id.', '.$related_value_id)
            ->get();

    }

    public static function replaceValuesForRelatedIds($moduleId, $data, $startAt = 0, $fields = null)
    {

        if ($fields == null) {
            $fields = Field::where('module_id', $moduleId)->get();
        }

        $relatedModules = [];
        $cnt = 0;
        foreach ($fields as $field) {
            if ($field->input_type == 'related') {
                $relatedModules[$cnt] = self::getRelatedModuleList(
                    $field->related_module_id,
                    $field->related_field_id,
                    $field->related_value_id
                );
            }
            $cnt++;
        }

        $cnt = 0;
        $returnData = [];
        foreach ($data as $data_key => $items) {
            if ($cnt >= $startAt) {
                $returnData[$data_key] = $items;
                foreach ($items as $key => $value) {
                    if (isset($relatedModules[$key])) {
                        if (isset($data[$data_key][$key])) {
                            if ($relatedModules[$key]
                                ->where('name', $data[$data_key][$key])
                                ->first() != null) {
                                $returnData[$data_key][$key] = $relatedModules[$key]
                                ->where('name', $data[$data_key][$key])
                                ->first()->id;
                            }

                        }

                    }
                }
            }
            $cnt++;
        }

        return $returnData;
    }

    public static function replaceRelatedIds($moduleId, $data, $fields = null)
    {

        if ($fields == null) {
            $fields = Field::where('module_id', $moduleId)->where('input_type', 'related')->get();
        }

        $relatedModules = [];
        foreach ($fields as $field) {
            $relatedModules[$field->name] = self::getRelatedModuleList(
                $field->related_module_id,
                $field->related_field_id,
                $field->related_value_id);
        }

        foreach ($data as $data_key => $items) {
            foreach ($items as $key => $value) {
                if (isset($relatedModules[$key])) {
                    if (isset($relatedModules[$key][$value])) {
                        foreach ($fields as $field) {
                            if ($field->name == $key) {
                                $data[$data_key]->{$key} = $relatedModules[$key][$value]->{$field->related_value_id};
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    public static function getRecord($moduleId = null, $id = null, $replaceRelatedIds = false)
    {
        $module = self::find($moduleId);
        $data = DB::table($module->name)
            ->selectRaw($module->name.'.'.'*, '.$module->name.'.'.'id as '.$module->name.'_row_id, '.$module->name.'.'.'id as row_id')
            ->where('id', $id)->first();

        if ($replaceRelatedIds) {
        $data = self::replaceRelatedIds($moduleId, $data);
        }

        return $data;
    }

    public static function getRecords($moduleId = null, $ids = [], $export = false)
    {
        $module = self::find($moduleId);
        $query = DB::table($module->name);
        $fieldNames = [];
        $fields = Field::where('module_id', $module->id)->where('status', 1)->get();
        foreach ($fields as $field) {
            $fieldNames[] = $module->name.'.'.$field->name;
        }

        $query->selectRaw(implode(',', $fieldNames));

        if (count($ids) > 0) {
            $query->whereIn('id', $ids)->get();
        }

        $data = $query->take(Setting::getSetting('max_export_records'))->get();

        if ($export) {
           $data = self::replaceRelatedIds($moduleId, $data);
            $headerData = $data->toArray();
            if (isset($headerData[0])) {
                $data->prepend(array_keys((array) $headerData[0]));
            }
        }

        return $data;
    }

    public static function getPreviousNext($moduleId, $recordId)
    {
        $previous = 0;
        $next = 0;

        $module = self::find($moduleId);
        $previous = DB::table($module->name)
            ->where('id', '<', $recordId)
            ->pluck('id')
            ->first();
        $next = DB::table($module->name)
            ->where('id', '>', $recordId)
            ->pluck('id')
            ->first();

        return [$previous, $next];
    }

    public static function validateRecords($module_id, $request)
    {
        foreach ($request as $key => $value) {
            $module_id = $key;
            if (isset($field_name)) {
                $module = Module::where('name', $module_id)->first();
                $field = Field::where('module_id', $module->id)
                    ->where('name', $field_name)
                    ->firstOrFail();
                if ($field) {
                    $data[$module->name][$field->name] = $value;
                    if (strlen($field->validation) > 0) {
                        $rules[$module->name][$field->name] = $field->validation;
                    }
                }
            }
        }
    }

    public static function deleteRecords($moduleId, $data)
    {
        Log::info('Deleting ModuleID '.$moduleId.'  Data: '.print_r($data));
        $module = self::find($moduleId);
        $results = DB::table($module->name)->whereIn('id', $data)->get()->toArray();
        event(new ModuleRecordDeleted($results));

        return DB::table($module->name)->whereIn('id', $data)->delete();
    }

    public static function saveRecord($module_id, $request, $returnId = false)
    {
        $faker = Factory::create();
        $data = [];
        $rules = [];
        $relationship = null;

        $recordId = isset($request['record_id']) ? $request['record_id'] : 0;
        $relationshipId = isset($request['relationship_id']) ? $request['relationship_id'] : 0;
        if (intval($relationshipId) > 0) {
            $relationship = Relationship::find($relationshipId) ?: null;
        }

        foreach ($request as $key => $value) {
            $pieces = explode('__', $key);
            if (isset($pieces[1])) {

                $field = Field::where('module_id', $pieces[0])->where('name', $pieces[1])->first();
                if ($field) {
                    if ($field->input_type == 'password') {
                        $value = Hash::make($value);
                    } elseif ($field->input_type == 'date') {
                        $value = strtotime($value);
                    } elseif ($field->input_type == 'checkbox') {
                        $value = (bool) $value;
                    }
                    $data[$pieces[0]][$field->name] = $value;

                    if (strlen($field->validation) > 0) {
                        $rules[$pieces[0]][$field->name] = $field->validation;
                    }
                }
            }
        }
        $id = '';
        $modules = [];

        foreach ($data as $key => $value) {
            if (isset($rules[$key])) {
                $validator = Validator::make($value, $rules[$key]);
                if ($validator->fails()) {
                    $e = new ValidationException($validator);
                    throw $e::withMessages($validator->errors()->toArray());
                }
            }

            $module = Module::find($key);
            if (intval($recordId) > 0) {

                if (intval($relationshipId) > 0) {
                   // DB::table($module->name)->update($value)->where('id', $recordId);
                } else {
                    $value['updated_at'] = Carbon::now();
                    DB::table($module->name)->where('id', $recordId)->update($value);
                    $id = $recordId;

                }

            } else {
                $value['created_at'] = Carbon::now();
                $value['updated_at'] = Carbon::now();
                $value['slug'] = $faker->regexify('[A-Za-z0-9]{20}');
                $id = DB::table($module->name)->insertGetId($value);
                WorkFlowData::insert([
                    'from_id' => isset($request['from_id']) ? $request['from_id'] : 0,
                    'from_module_id' => isset($request['from_module']) ? $request['from_module'] : 0,
                    'to_id' => $id,
                    'to_module_id' => $module->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            $modules[] = $key;
        }

        if (count($modules) > 1) {
            Log::info('Saving Modules '.print_r($modules).'  relationship: '.print_r($relationship));
            event(new ModuleRecordSaved($modules, $relationship));
            $id = Relationship::updateOrInsert($modules, $relationship);
        }

        return $id;
    }

    public static function insertImport($module, $data)
    {
        $faker = Factory::create();
        $arr = [];
        $ids = [];
        foreach ($data as $row) {
            Field::where('module_id', $module->id)->get()->each(function ($item) use (&$arr, &$row) {
                $singleItem = array_shift($row);
                if ($item->input_type == 'checkbox' && intval($singleItem) < 1) {
                $singleItem = 0;
                }
                $arr[$item->name] = $singleItem;
            });
            $arr['slug'] = $faker->regexify('[A-Za-z0-9]{20}');
            $ids[] = DB::table($module->name)->insertGetId($arr);
        }

        return $ids;
    }
}
