<?php

use App\Exports\GenericExport;
use App\Imports\GenericImport;
use App\Models\AIAssist;
use App\Models\Connector;
use App\Models\ConnectorCommand;
use App\Models\Datalet;
use App\Models\Field;
use App\Models\Module;
use App\Models\ModuleSubpanel;
use App\Models\Permission;
use App\Models\Endpoint;
use App\Models\Relationship;
use App\Models\RelationshipModule;
use App\Models\Search;
use App\Models\ModuleConvertable;
use App\Models\WorkFlowData;
use App\Models\IceHelp;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

$deleteConnectorWithCommands = function ($id) {
    $connector = Connector::find($id);

    if ($connector) {
        $connector->commands()->delete();
        $connector->delete();

        return response()->json(['status' => 'Connector and its commands deleted successfully']);
    }

    return response()->json(['status' => 'Connector not found'], 404);
};

Route::get('help', function (Request $request) {
    $slug = $request->query('slug');

    $help = IceHelp::where('slug', $slug)->first();

    if (! $help) {
        return response()->json(['error' => 'Help content not found'], 404);
    }

    return response()->json($help);
})->middleware(['auth', 'verified'])->name('data')
    ->name('help');

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
    ->name('delete_record');

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
    ->name('subpanel_record_save');

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

Route::any('ai_assist/fields/{module_id}', function (Request $request, $id = 0) {
    $data = AiAssist::suggestFields($id, $request->all());
    return response()->json($data);
})->middleware(['auth', 'verified'])->name('ai_assist')
    ->name('ai_assist');

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
    ->name('data_related_fields');

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



Route::match(['post', 'delete'], 'connector/delete_command/{command_id}', function ($commandId) {

    ConnectorCommand::where('id', $commandId)->delete();

    return response()->json(Connector::all());

})->middleware(['auth', 'verified', 'admin'])->name('data')
    ->name('delete_endpoint');

Route::get('connectors', function () {

    return response()->json(Connector::all());
})->middleware(['auth', 'verified', 'admin'])->name('data')
    ->name('connectors');




// Route for Saving the Connector
Route::post('connector/set_connector', function (Request $request) {
    $data = $request->only([
        'name', 'auth_type', 'auth_key', 'base_url', 'token_url',
        'client_id', 'client_secret', 'username', 'password',
        'access_token', 'refresh_token', 'token_expires_at', 'status'
    ]);


    // Validate and/or save the connector here
    $connector = Connector::updateOrCreate(
        ['id' => $request->input('id')],
        $data
    );

    return response()->json([
        'status' => 'Connector saved successfully',
        'message' => 'Connector saved successfully',
        'id' => $connector->id,
    ]);
})->middleware(['auth', 'verified', 'admin'])->name('set_connector');

// Route for Deleting the Connector
Route::delete('connector/delete_connector/{id}', function ($id) use ($deleteConnectorWithCommands) {
    return $deleteConnectorWithCommands($id);
})->middleware(['auth', 'verified', 'admin'])->name('delete_connector');

Route::delete('connectors/{id}', function ($id) use ($deleteConnectorWithCommands) {
    return $deleteConnectorWithCommands($id);
})->middleware(['auth', 'verified', 'admin'])->name('delete_connector_short');

// Add Command
Route::post('connector/add_command', function(Request $request) {
    $request->validate([
        'connector_id' => 'required|integer',
        'name' => 'required|string',
        'method_name' => ['required', 'string', 'regex:/^[A-Za-z_][A-Za-z0-9_]*$/'],
        'description' => 'required|string',
        'endpoint_id' => 'nullable|integer',
        'status' => 'boolean',
        'class_name' => ['required', 'string', 'regex:/^[A-Za-z_][A-Za-z0-9_]*$/']
    ]);

    $fullClass = "App\\Connectors\\" . ucfirst($request->class_name) . "Connector";
    if (!class_exists($fullClass)
        || !is_subclass_of($fullClass, \App\Connectors\BaseConnector::class)
        || !in_array($request->method_name, $fullClass::allowedCommands(), true)) {
        return response()->json(['message' => 'Connector command class or method is not allowed.'], 422);
    }

    $command = new ConnectorCommand();
    $command->connector_id = $request->connector_id;
    $command->name = $request->name;
    $command->method_name = $request->method_name;
    $command->description = $request->description;
    $command->endpoint_id = $request->endpoint_id;
    $command->status = $request->status ?? true;
    $command->class_name = $request->class_name; // save class_name
    $command->save();

    return response()->json(['command' => $command]);
})->middleware(['auth', 'verified', 'admin'])->name('add_command');

// Update Command
Route::post('connector/update_command/{id}', function(Request $request, $id) {
    $command = ConnectorCommand::findOrFail($id);

    $request->validate([
        'name' => 'required|string',
        'method_name' => ['required', 'string', 'regex:/^[A-Za-z_][A-Za-z0-9_]*$/'],
        'description' => 'required|string',
        'endpoint_id' => 'nullable|integer|exists:endpoints,id',
        'status' => 'boolean',
        'class_name' => ['required', 'string', 'regex:/^[A-Za-z_][A-Za-z0-9_]*$/']
    ]);

    $fullClass = "App\\Connectors\\" . ucfirst($request->class_name) . "Connector";
    if (!class_exists($fullClass)
        || !is_subclass_of($fullClass, \App\Connectors\BaseConnector::class)
        || !in_array($request->method_name, $fullClass::allowedCommands(), true)) {
        return response()->json(['message' => 'Connector command class or method is not allowed.'], 422);
    }

    $command->name = $request->name;
    $command->method_name = $request->method_name;
    $command->description = $request->description;
    $command->endpoint_id = $request->endpoint_id;
    $command->status = $request->status ?? true;
    $command->class_name = $request->class_name; // save class_name
    $command->save();

    return response()->json(['command' => $command]);
})->middleware(['auth', 'verified', 'admin'])->name('update_command');



Route::get('endpoints/{connector_id}', function (Request $request, $connectorId) {
    $endpoints = Endpoint::where('connector_id', $connectorId)->get();

    return response()->json($endpoints);
})->middleware(['auth', 'verified', 'admin'])->name('connector_endpoints');

Route::get('commands/{connector_id}', function (Request $request, $connectorId) {
    $commands = ConnectorCommand::where('connector_id', $connectorId)->get();
    return response()->json($commands);
})->middleware(['auth', 'verified', 'admin'])->name('connector_commands');

Route::post('endpoints/add', function (Request $request) {
    $data = $request->all();

    $endpoint = new Endpoint();
    $endpoint->connector_id = $data['connector_id'] ?? null;
    $endpoint->name = $data['name'] ?? '';
    $endpoint->endpoint = $data['endpoint'] ?? '';
    $endpoint->request_type = $data['request_type'] ?? 'GET';
    $endpoint->class_name = $data['class_name'] ?? '';
    $endpoint->params = $data['params'] ?? '';
    $endpoint->headers = $data['headers'] ?? '';
    $endpoint->status = isset($data['status']) ? (bool)$data['status'] : true;
    $endpoint->save();

    return response()->json(['id' => $endpoint->id]);
})->middleware(['auth', 'verified', 'admin']);


Route::post('endpoints/update/{id}', function (Request $request, $id) {
    $endpoint = Endpoint::findOrFail($id);
    $data = $request->all();

    $endpoint->name = $data['name'] ?? $endpoint->name;
    $endpoint->endpoint = $data['endpoint'] ?? $endpoint->endpoint;
    $endpoint->request_type = $data['request_type'] ?? $endpoint->request_type;
    $endpoint->class_name = $data['class_name'] ?? $endpoint->class_name;
    $endpoint->params = $data['params'] ?? $endpoint->params;
    $endpoint->headers = $data['headers'] ?? $endpoint->headers;
    $endpoint->status = isset($data['status']) ? (bool)$data['status'] : $endpoint->status;
    $endpoint->save();

    return response()->json(['message' => 'Endpoint updated successfully']);
})->middleware(['auth', 'verified', 'admin']);


Route::post('workflow/save', function (Request $request) {

    $workflow = $request->input('workflow', []);

    // Get current module IDs in DB
    $existingIds = ModuleConvertable::pluck('module_id')->toArray();

    $incomingIds = collect($workflow)->pluck('module_id')->toArray();

    // Remove modules that are no longer in the workflow
    ModuleConvertable::whereNotIn('module_id', $incomingIds)->delete();

    // Insert or update modules in workflow
    foreach ($workflow as $index => $item) {
        ModuleConvertable::updateOrCreate(
            ['module_id' => $item['module_id']],
            [
                'primary_module_id' => $item['module_id'], // adjust if needed
                'level' => $index + 1,
            ]
        );
    }

    return response()->json(['success' => true]);
})->middleware(['auth', 'verified']);

Route::delete('workflow/{id}', function ($id) {
    \App\Models\ModuleConvertable::findOrFail($id)->delete();
    return response()->json(['success' => true]);
})->middleware(['auth', 'verified']);


Route::get('connector/command/{id}', function ($id) {
    return ['command' => \App\Models\ConnectorCommand::findOrFail($id)];
})->middleware(['auth', 'verified', 'admin'])->name('view_command');
