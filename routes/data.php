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



Route::get('connector/delete_command/{command_id}', function ($commandId) {

    ConnectorCommand::where('id', $commandId)->delete();

    return response()->json(Connectors::all());

})->middleware(['auth', 'verified'])->name('data')
    ->name('delete_endpoint');

Route::get('connector/delete_connector/{connector_id}', function ($connectorId) {

    ConnectorCommand::where('connector_id', $connectorId)->delete();
    Connector::where('id', $connectorId)->delete();

    return response()->json(Connectors::all());
})->middleware(['auth', 'verified'])->name('data')
    ->name('delete_connector');

Route::get('connectors', function () {

    return response()->json(Connectors::all());
})->middleware(['auth', 'verified'])->name('data')
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

    return response()->json(['status' => 'Connector saved successfully', 'connector' => $connector]);
})->middleware(['auth', 'verified'])->name('set_connector');

// Route for Deleting the Connector
Route::delete('connector/delete_connector/{id}', function ($id) {
    $connector = Connector::find($id);

    if ($connector) {
        // Delete all associated ConnectorCommands
        $connector->commands()->delete();
        // Delete the connector
        $connector->delete();

        return response()->json(['status' => 'Connector and its commands deleted successfully']);
    } else {
        return response()->json(['status' => 'Connector not found'], 404);
    }
})->middleware(['auth', 'verified'])->name('delete_connector');

Route::post('connector/run_command', function (Request $request) {
    $commandId = $request->input('command_id');

    $custom_command=ConnectorCommand::where('id', $commandId)->with('connector')->firstOrFail();
    $className = $custom_command->connector['class'];

    // Define the full path to the class
    $classPath = app_path("Connectors/{$className}Connector.php");

    // Check if the class file exists
    if (File::exists($classPath)) {
        // Create the full namespace for the class
        $fullClassName = "App\\Connectors\\{$className}Connector";

        // Check if the class exists and is instantiable
        if (class_exists($fullClassName)) {
            $connectorInstance = new $fullClassName($custom_command);

            $response = $connectorInstance->{$custom_command->method_name}();
            $custom_command->last_updated=\Carbon\Carbon::now();
            $custom_command->last_output=$response;
            $custom_command->save();

            return response()->json([
                'status' => 'success',
                'message' => $response,
                'class' => $fullClassName,
                'instance' => $connectorInstance,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => "Class {$fullClassName} is not instantiable",
            ], 500);
        }
    } else {
        return response()->json([
            'status' => 'error',
            'message' => "Class {$className} does not exist in /app/Connectors",
        ], 404);
    }

})->middleware(['auth', 'verified'])->name('run_command');



// Route for Updating a Command
Route::post('connector/update_command/{id?}', function ($id, Request $request) {
    $command = ConnectorCommand::find($id);

    if ($command) {
        $data = $request->only(['name', 'method_name', 'description', 'status']);
        $command->update($data);

        return response()->json(['status' => 'Command updated successfully']);
    } else {
        return response()->json(['status' => 'Command not found'], 404);
    }
})->middleware(['auth', 'verified'])->name('update_command');

// Route for Adding a New Command
Route::post('connector/add_command', function (Request $request) {
    $data = $request->only(['name', 'method_name', 'description', 'status', 'connector_id']);

    $command = ConnectorCommand::create($data);

    return response()->json(['status' => 'Command added successfully', 'command' => $command]);
})->middleware(['auth', 'verified'])->name('add_command');

// Route for Deleting a Command
Route::post('connector/delete_command/{id}', function ($id) {
    $command = ConnectorCommand::find($id);

    if ($command) {
        $command->delete();
        return response()->json(['status' => 'Command deleted successfully']);
    } else {
        return response()->json(['status' => 'Command not found'], 404);
    }
})->middleware(['auth', 'verified'])->name('delete_command');


Route::get('endpoints/{connector_id}', function (Request $request, $connectorId) {
    $endpoints = Endpoint::where('connector_id', $connectorId)->get();

    return response()->json($endpoints);
})->middleware(['auth', 'verified'])->name('connector_endpoints');

Route::get('commands/{connector_id}', function (Request $request, $connectorId) {
    $commands = Command::where('connector_id', $connectorId)->get();
    return response()->json($commands);
})->middleware(['auth', 'verified'])->name('connector_commands');

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
})->middleware(['auth', 'verified']);

// Update existing endpoint
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
})->middleware(['auth', 'verified']);


