<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema as Schema;


class Relationship extends Model
{
    use HasFactory;
    protected $table = 'ice_relationships';

    /*
    public function fields()
    {
        return $this->hasMany(RelationshipModule::class)
            ->with('modulefields')->get();
    }
    */


    public function relationshipmodule()
    {
        return $this->hasMany(RelationshipModule::class);

    }

    public static function getRecord($relationshipId = null, $id = null)
    {
        $relationship = Relationship::find($relationshipId);
        return DB::table($relationship->name)
            ->where('id', $id)
            ->first();
    }

    public static function saveRecord($relationship_id, $records = [], $record_id=0)
    {
        $data = [];
        $relationship=Relationship::where('id', $relationship_id)->first();
        foreach($records as $key => $value) {
            $module = Module::where('id', $key)->firstOrFail();
            $data[$module->name . '_id'] = $value;
        }
        return DB::table($relationship->name)->updateOrInsert(['id' => $record_id],$data);
    }

    public static function updateOrInsert($modules, $relationship)
    {
        $moduleNames = [];
        $relatedFieldTypes = [];
        Module::whereIn('id', $modules)->each(function ($module)  use (&$moduleNames, &$relatedFieldTypes) {
            $moduleNames[]=strtoupper($module->name);
            $relatedFieldTypes[]='integer';
        });
        $moduleName=implode("_", $moduleNames);

        if(!isset($relationship))
        {
            $relationship = new Relationship();
        }

        $relationship->name = $moduleName;
        $relationship->modules = implode(",", $modules);
        $relationship->related_field_types = implode(",", $relatedFieldTypes);
        $relationship->status = 1;
        $relationship->save();

        foreach($modules as $module_id)
        {
            RelationshipModule::where('relationship_id', $relationship->id)
                ->where('module_id', $module_id)
                ->firstOrCreate(['relationship_id' => $relationship->id, 'module_id' => $module_id]);
        }
        return $relationship;
    }

    public function generate($seed = 0)
    {

        $this->where('status', 1)->get()->each(function ($relationship) use ($seed)  {
            if(Schema::hasTable($relationship->name))
                Schema::drop($relationship->name);

            Schema::create($relationship->name, function ($table) use ($relationship) {
                $table->id();
                $modules=explode(",",$relationship->modules);
                collect($modules)->each(function ($module_id, $key) use ($table, $relationship) {
                    $module = Module::find($module_id);
                    $related_field_types=explode(",", $relationship->related_field_types);
                    $related_field_type='integer';
                    if(isset($related_field_types[$key])) $related_field_type=$related_field_types[$key];
                    $table->{$related_field_type}($module->name . '_id')->unsigned();
                });
                $table->integer('status')->default(1);
                $table->timestamps();
            });

            if($seed > 0){
                for($x=0;$x<$seed;$x++)
                {
                    $modules=explode(",",$relationship->modules);

                    $data=[];
                    $mod_id=rand(1, 6);
                    collect($modules)->each(function ($module_id, $key) use ($relationship, $mod_id, &$data) {
                        $module = Module::find($module_id);
                        $data[$module->name . '_id'] = $mod_id;
                    });
                    DB::table($relationship->name)->insert($data);

                    $data=[];
                    collect($modules)->each(function ($module_id, $key) use ($relationship, $mod_id, &$data) {
                        $module = Module::find($module_id);
                        if($key == 0) $data[$module->name . '_id'] = $mod_id;
                        else $data[$module->name . '_id'] = rand(2, 6);
                    });

                    $data=[];
                    collect($modules)->each(function ($module_id, $key) use ($relationship, $mod_id, &$data) {
                        $module = Module::find($module_id);
                        if($key == 0) $data[$module->name . '_id'] = $mod_id;
                        else $data[$module->name . '_id'] = rand(3, 6);
                    });

                    $data=[];
                    collect($modules)->each(function ($module_id, $key) use ($relationship, $mod_id, &$data) {
                        $module = Module::find($module_id);
                        if($key == 1) $data[$module->name . '_id'] = $mod_id;
                        else $data[$module->name . '_id'] = rand(2, 6);
                    });
                }


            }
        });

        $this->generateRelationshipModules();
    }

    public function getRelatedData($primaryId, $relationshipId = 0)
    {
        $relationship = $this->find($relationshipId);
        $relatedData = DB::table($relationship->name)
            ->where('primary_id', $primaryId)
            ->get();
        return $relatedData;
    }

    public function getRelatedModules($moduleId)
    {
        return DB::table('relationships')
            ->where('module_id', $moduleId)
            ->get();

    }

    public function findRelationshipId($moduleId, $relatedModuleId)
    {
        $relationshipId=$this->where('module_id', $moduleId)
            ->where('related_module_id', $relatedModuleId)->first()->id;
        if(!$relationshipId) {
            $relationshipId=$this->where('module_id', $relatedModuleId)
                ->where('related_module_id', $moduleId)->first()->id;
        }

        return $relationshipId;
    }

    public static function deleteRecords($relationshipId, $data)
    {
        Log::info("Deleting RelationshipID " . $relationshipId);
        $relationship=self::findOrFail($relationshipId);
        return DB::table($relationship->name)->whereIn('id', $data)->delete();
    }

    public function generateRelationshipModules(){
        RelationshipModule::truncate();
        $relationships=Relationship::all();
        $relationships->each(function ($relationship) {
            $models=explode(",", $relationship->modules);
            foreach($models as $model_id)
            {
                $Record=RelationshipModule::where('relationship_id', $relationship->id)
                    ->where('module_id', $model_id)
                    ->firstOrCreate(['relationship_id' => $relationship->id, 'module_id' => $model_id]);
            }
        });
    }

    public static function getRelationshipRecord($relationship_id, $recordId){
        $output=[];
        $relationship=Relationship::where('id', $relationship_id)->first();
        $relationship_data=DB::table($relationship->name)
            ->where('id', $recordId)
            ->first();

        foreach($relationship_data as $key => $value)
        {
            $pieces=explode('_', $key);
            if(isset($pieces[1]) && $pieces[1] == 'id')
            {
                $module=Module::where('name', $pieces[0])->first();
            }
        }
        exit;
    }
}
