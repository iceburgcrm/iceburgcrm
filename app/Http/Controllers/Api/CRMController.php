<?php

namespace App\Http\Controllers\Api;


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\GenericImport;
use App\Models\Permission;
use App\Models\Relationship;
use App\Models\Search;
use Illuminate\Http\Request;
use App\Models\Module;
use Maatwebsite\Excel\Facades\Excel;

// Assuming you have a Customer model

class CRMController extends Controller
{
    public function index()
    {
        $modules = Module::where('status', 1)->get();
        return response()->json($modules, 200);
    }


    public function search(Request $request){
        return response()->json(Search::getData($request->all())->toArray(), 200);
    }

    public function show($id)
    {
        return response()->json(Module::find($id)->toArray(), 200);
    }

    public function updateoradd(Request $request, $id)
    {
        $data=$request->all();
        $module = Module::find($id);
        if (is_null($module)) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json(Module::saveRecord($id, $data), 200);
    }

    public function destroy($id, $type='module', Request $request)
    {
        $recordIds = $request->input('record_ids');

        $moduleId = $id;
        if (is_null($moduleId)) {
            return response()->json(['message' => 'module id is invalid'], 404);
        }
        if ($type == 'relationship') {
            $moduleId = RelationshipModule::where('relationship_id', $moduleId)->value('module_id');
        }
        if (! Permission::checkPermission($moduleId, 'write')) {
            return response()->json(['error' => 'No Access'], 422);
        }

        $status = false;
        switch ($type) {
            case 'module':
                $status = Module::deleteRecords($moduleId, $recordIds);
                break;
            case 'relationship':
                $status = Relationship::deleteRecords($moduleId, $recordIds);
                break;
            default:
                break;
        }
        return response()->json(['message' => 'Data deleted'], 204);
    }
}
