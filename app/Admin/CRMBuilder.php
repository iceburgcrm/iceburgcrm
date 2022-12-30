<?php
namespace App\Admin;

use App\Models\Field;
use App\Models\Module;
use App\Models\ModuleSubpanel;
use App\Models\Datalet;
use App\Models\Relationship;
use App\Models\Relationships;
use App\Models\SubpanelField;
use Illuminate\Support\Facades\Log;


class CRMBuilder
{
    public function __construct()
    {

    }

    public static function process($id, $type, $request)
    {
        $data = [];
        $status=0;
        switch ($type) {
            case 'get_modules':
                $data = [
                    'modules' => Module::get()->toArray()
                ];
                break;
            case 'get_datalets':
                $data = [
                    'datalets' => Datalet::get()->toArray()
                ];
                break;
            case 'get_relationships':
                $data = [
                    'relationships' => Relationship::get()->toArray()
                ];
                break;
            case 'select_subpanel_fields':
                $subpanel=ModuleSubpanel::where('id', $id)
                    ->with('relationship')
                    ->first();

                $modules=explode(",",$subpanel->relationship->modules);
                $data=Field::whereIn('module_id', $modules)
                    ->with('module')
                    ->get();
                break;
            case 'select_module':
                $module = Module::find($id);

                $relationships=[];
                Relationship::where('modules', 'LIKE', '%' . $id . '%')
                    ->get()
                    ->each(function ($relationship) use (&$relationships, $id){
                        $modules=explode(',', $relationship->modules);
                        if(in_array($id, $modules)){
                            $relationships[]=$relationship->toArray();
                        }
                    });

                $data = [
                    'module' => Module::where('id', $id)->first(),
                    'fields' => Field::where('module_id', $id)->get()->toArray(),
                    'subpanels' => ModuleSubpanel::where('module_id', $id)
                        ->with('subpanelfields')->with('relationship')
                        ->get(),
                    'relationships' => $relationships
                ];
                break;
            case 'regenerate':
                $module = new Module;
                if($request->module_id == 0)
                {
                    Log::info("Regenerating all modules");
                    $status = $module->generate($request->seed);
                    $status = 1;
                }
                elseif($request->module_id > 0)
                {
                    $status = $module->generate($request->seed, $request->module_id);
                }
                $data = ['status' => $status];
                break;
            case 'add_subpanel_field':
                $status=SubpanelField::insert([
                   'field_id' => $request->subpanel_field_id,
                   'subpanel_id' => $request->subpanel_id
                ]);
                $data = ['status' => $status];
                break;
            case 'add_subpanel':
                $status = ModuleSubpanel::insert([['name' => $request->name,
                    'label' => ucwords(
                        strtolower(
                            str_replace('_', ' ', $request->name)
                        )
                    ),
                    'module_id' => $id,
                    'status' => 0]]);
                $data = ['status' => $status];
                break;
            case 'add_field':
                $status = Field::insert([['name' => $request->name,
                    'label' => ucwords(
                        strtolower(
                            str_replace('_', ' ', $request->name)
                        )
                    ),
                    'data_type' => 'string',
                    'field_length' => 64,
                    'status' => 0,
                    'module_id' => $id
                ]]);
                $data = ['status' => $status];
                break;
            case 'add_relationship':
                $status = Relationship::insert([['name' => $request->name, 'modules' => $request->relationship_modules, 'status' => 0]]);
                $data = ['status' => $status];
                break;
            case 'add_module':
                $status = Module::insert([['name' => $request->name,
                    'label' => ucwords(
                        strtolower(
                            str_replace('_', ' ', $request->name)
                        )
                    ),
                    'module_group_id' => 6,
                    'status' => 0,
                    'create_table' => 1,
                    'icon' => 'CircleStackIcon'
                ]]);
                $data = ['status' => $status];
                break;
            case 'add_datalet':
                $status = Datalet::insert([['name' => $request->name,
                    'label' => ucwords(
                        strtolower(
                            str_replace('_', ' ', $request->name)
                        )
                    ),
                    'active' => 0]]);
                $data = ['status' => $status];
                break;
            case 'delete':
                if($request->type == 'module')
                {
                    Module::where('id', $request->delete_id)->delete();
                    Field::where('module_id', $request->delete_id)->delete();
                    ModuleSubpanel::where('module_id', $request->delete_id)->delete();
                    $status=1;
                    $data = ['status' => $status];
                }
                elseif($request->type == 'field')
                {
                    $status=Field::where('id', $request->delete_id)->delete();
                    $data = ['status' => $status];
                }
                elseif($request->type == 'subpanel')
                {
                    ModuleSubpanel::where('id', $request->delete_id)->delete();
                    $data = ['status' => $status];
                }
                elseif($request->type == 'relationship')
                {
                    $status=Relationship::where('id', $request->delete_id)->delete();
                    $data = ['status' => $status];
                }
                elseif($request->type == 'datalet')
                {
                    $status=Datalet::where('id', $request->delete_id)->delete();
                }
                $data = ['status' => $status];
                break;
            case 'create':
                //$request->type;
                $data = ['status' => $status];
                break;
            case 'save':
                $status = 0;
                switch ($request->type) {
                    case 'module':
                        Module::where('id', $request->type_id)
                            ->update([$request->key => $request->value]);
                        $status = 1;
                        break;
                    case 'field':
                        Field::where('id', $request->type_id)
                            ->update([$request->key => $request->value]);
                        $status = 1;
                        break;
                    case 'subpanel':
                        ModuleSubpanel::where('id', $request->type_id)
                            ->update([$request->key => $request->value]);
                        $status = 1;
                        break;
                    case 'relationship':
                        Relationship::where('id', $request->type_id)
                            ->update([$request->key => $request->value]);
                        $status = 1;
                        break;
                    case 'datalet':
                        Datalet::where('id', $request->type_id)
                            ->update([$request->key => $request->value]);
                        $status = 1;
                        break;
                    default:
                        break;
                }
                $data = ['status' => $status];
                break;
            default:
                break;
        }
        return $data;

    }
}
