<?php

use App\Exports\GenericExport;
use App\Imports\GenericImport;
use App\Models\Datalet;
use App\Models\Field;
use App\Models\Module;
use App\Models\ModuleSubpanel;
use App\Models\Permission;
use App\Models\Relationship;
use App\Models\RelationshipModule;
use App\Models\Search;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('datalet', function (Request $request) {
    $datalet=Datalet::where('id', $request->id)->firstOrFail();
    return response()->json($datalet->getData());
})->middleware(['auth', 'verified'])->name('data')
    ->name('dashlet');

Route::post('delete/{base_id}/type/{type}', function (Request $request, $baseId, $type = 'module') {
    $moduleId = $baseId;
    if ($type == 'relationship') {
        $moduleId = RelationshipModule::where('relationship_id', $baseId)->value('module_id');
    }
    if (! Permission::checkPermission($moduleId, 'write')) {
        return response()->json(['error' => 'No Access'], 422);
    }

    $status = false;
    switch ($type) {
        case 'module':
            $status = Module::deleteRecords($baseId, $request->toArray());
            break;
        case 'relationship':
            $status = Relationship::deleteRecords($baseId, $request->toArray());
            break;
        default:
            break;
    }

    return response()->json(
        ['success' => $status]
    );
})->middleware(['auth', 'verified'])
    ->name('get_record');

Route::get('module/{module_id}/record/{record_id}', function ($moduleId = 0, $recordId = 0) {
    if (! Permission::checkPermission($moduleId)) {
        return response()->json(['error' => 'No Access'], 422);
    }

    return response()->json(
        Collect(Module::getRecord($moduleId, $recordId))->slice(0, -6)->toArray()
    );
})->middleware(['auth', 'verified'])
    ->name('get_record');

Route::get('search_fields/{value}/search_type/{type}', function ($moduleID = '', $type = 'relationship') {

    $module = Module::where('id', $moduleID)->firstOrFail();
    $fields = Search::getFields($module->id, $type);

    return response()->json($fields);

})->middleware(['auth', 'verified'])
    ->name('relationship_fields');

Route::any('download/{module_id}/{type}', function (Request $request, $moduleId = 1, $type = 'xlsx') {
    if (! Permission::checkPermission($moduleId, 'export')) {
        return response()->json(['error' => 'No Access'], 422);
    }
    $data = new Collection(
        Module::getRecords($moduleId, $request->all(), true)->toArray()
    );

    return Excel::download(new GenericExport($data), 'users.'.$type);
})->middleware(['auth', 'verified'])
    ->name('record_save');

Route::any('save', function (Request $request) {
    $data = $request->all();
    if (! Permission::checkPermission($data['module_id'], 'write')) {
        return response()->json(['error' => 'No Access'], 422);
    }

    return response()->json(Module::saveRecord($data['module_id'], $data));
})->middleware(['auth', 'verified'])
    ->name('module_record_save');

Route::any('subpanel/save', function (Request $request) {
    [$subpanelId, $selectedRecords, $newRecords, $recordId] = ModuleSubpanel::parseRequest($request);
    $subpanel = ModuleSubpanel::find($subpanelId);
    if (! Permission::checkPermission($subpanel->module_id, 'write')) {
        return response()->json(['error' => 'No Access'], 422);
    }

    if ($subpanelId) {
        return response()->json(
            ModuleSubpanel::processRecords($subpanelId, $selectedRecords, $newRecords, $recordId)
        );
    }

    return response()->json(['error' => 'Missing Subpanel ID'], 500);

})->middleware(['auth', 'verified'])
    ->name('module_record_save');

Route::any('import', function (Request $request) {

    $temp_file = request()->file('input_file')->store('temp');
    $fields = [];

    $firstRowHeader = 0;
    if ($request->first_row_header === 'true') {
        $firstRowHeader = 1;
    }
    $import = new GenericImport();
    Excel::import($import, $temp_file);
    $data = $import->data;

    if ($request->input('module_id')) {
        $module = Module::where('id', $request->input('module_id'))->firstOrFail();
        if (! Permission::checkPermission($module->id, 'import')) {
            return response()->json(['error' => 'No Access'], 422);
        }

        $fields = Field::where('module_id', $module->id)
            ->where('status', 1)
            ->get()
            ->pluck('name')
            ->toArray();
    } else {
        foreach ($import->data[0] as $item) {
            $fields[] = $item;
        }
    }

    if ($request->input('preview', 0) == true) {

        return response()->json([
            'preview' => 1,
            'fields' => $fields,
            'row' => $import->data[0][$firstRowHeader],
        ]
        );
    }
    $data = Module::replaceValuesForRelatedIds($module->id, $data[0], $firstRowHeader);
    $results = Module::insertImport($module, $data);

    return response()->json([
        'records_updated' => count($data),
    ]
    );
})->middleware(['auth', 'verified'])
    ->name('module_record_import');

Route::get('datalet', function (Request $request) {
    $datalet=Datalet::where('id', $request->id)->firstOrFail();
    return response()->json($datalet->getData());
})->middleware(['auth', 'verified'])->name('data')
    ->name('dashlet');

Route::get('search_data', function (Request $request) {
    return response()->json(Search::getData($request->all())->toArray());
})->middleware(['auth', 'verified'])->name('search_data');

Route::get('subpanel/{subpanel_id}', function (Request $request, $id = 0) {
    $subpanel = ModuleSubpanel::getSubpanelData($id, $request->all());

    return response()->json($subpanel);
})->middleware(['auth', 'verified'])->name('data')
    ->name('subpanel_relationship_fields');

Route::get('related_fields/field_id/{field_id}', function ($fieldId = 0) {

    $field = Field::find($fieldId);
    $relatedModule = Module::find($field->related_module_id);
    $records = DB::table($relatedModule->name)->select($field->related_field_id, $field->related_value_id)->get();
    if (! $records) {
        $records = 'Unknown';
    }

    return response()->json($records->toArray());

})->middleware(['auth', 'verified'])->name('data')
    ->name('data_related');

Route::get('related_field_name/field_id/{field_id}/value/{value}', function (Request $request, $fieldId = 0, $value = 0) {
    $field = Field::find($fieldId);
    $relatedModule = Module::find($field->related_module_id);

    $record = DB::table($relatedModule->name)->where($field->related_field_id, $value)->value($field->related_value_id);
    if (! $record) {
        $record = 'Unknown';
    }

    return response()->json($record);

})->middleware(['auth', 'verified'])->name('data')
    ->name('data_related');
