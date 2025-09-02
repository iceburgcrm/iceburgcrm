<?php

use App\Admin\CRMBuilder;
use App\Http\Integrations\ApiCall;
use App\Http\Integrations\Generic\GenericAPI;
use App\Http\Integrations\Generic\Requests\ApiRequest;
use App\Models\Admin;
use App\Models\ConnectorCommand;
use App\Models\Endpoint;
use App\Models\Permission;
use App\Models\Setting;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Connector;

Route::get('module', function ($request) {
    return response()->json(Admin::getData($request));
})->middleware(['auth', 'verified'])
    ->name('admin_relationship_fields');

Route::any('permissions', function () {
    return response()->json(
        Permission::with('modules')->with('roles')->get()
    );
})->middleware(['auth', 'verified'])
    ->name('permissions_data');

Route::any('permissions/save', function (Request $request) {
    return response()->json(
        Permission::where('id', $request->input('id', 0))->
        update([
            'can_'.$request->input('type', 'read') => $request->current_state === 0 ? 1 : 0,
        ])
    );
})->middleware(['auth', 'verified'])
    ->name('permission_save');

Route::any('builder/{id}/type/{type}', function (Request $request, $id, $type) {
    $data = CRMBuilder::process($id, $type, $request);

    return response()->json($data);
})->middleware(['auth', 'verified'])->name('data')
    ->name('builder_data');

Route::post('settings', function (Request $request) {
    return response()->json(Setting::saveSettings($request->all()));
})->middleware(['auth', 'verified'])->name('settings_save');

Route::post('resetcrm', function (Request $request) {
    return response()->json(['status' => Admin::resetCRM()]);
})->middleware(['auth', 'verified'])->name('reset_crm');

Route::get('commands/run/{id}', function (Request $request, $id) {

    $endpoint = ConnectorCommand::find($id);
    $customData = [
        'key1' => 'value1',
        'key2' => 'value2',
    ];

    $apiService = new ApiService($customData);
    $response = $apiService->makeRequest($endpoint);
    return response()->json($response);

})->middleware(['auth', 'verified'])->name('endpoint_run');

Route::get('sendrequest', function (Request $request, $id) {

    $endpoint = Endpoint::find($id);
    $customData = [
        'key1' => 'value1',
        'key2' => 'value2',
    ];

    $apiService = new ApiService($customData);
    $response = $apiService->makeRequest($endpoint);
    return response()->json($response);
    /*
    $apicall = new ApiCall('https://official-joke-api.appspot.com/random_joke');
    $res = $apicall->send();
    $items = $res->collect()->toArray();
    dd($items);

    $connector = new GenericAPI(
        'https://official-joke-api.appspot.com'
    );
    $apiRequest = new ApiRequest('/random_joke');
    $res = $connector->send($apiRequest);
    $body = $res->json();
    dd($body);

    return response()->json(['status' => 1, 'data' => $requestApi->send(),
    ]);
    */
})->middleware(['auth', 'verified'])->name('sendrequest');







