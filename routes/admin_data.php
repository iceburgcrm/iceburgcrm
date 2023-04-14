<?php

use App\Admin\CRMBuilder;
use App\Http\Integrations\ApiCall;
use App\Http\Integrations\Generic\GenericAPI;
use App\Http\Integrations\Generic\Requests\ApiRequest;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('module', function ($request) {
    return response()->json(Admin::getData($request));
})->middleware(['auth', 'verified'])
    ->name('relationship_fields');

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
})->middleware(['auth', 'verified'])->name('settings_save');

Route::get('sendrequest', function (Request $request) {

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
})->middleware(['auth', 'verified'])->name('sendrequest');

Route::get('connector/delete_endpoint/{endpoint_id}', function ($endpointId) {

    Endpoint::where('id', $endpointId)->delete();

    return response()->json(Connectors::all());

})->middleware(['auth', 'verified'])->name('data')
    ->name('delete_endpoint');

Route::get('connector/delete_connector/{connector_id}', function ($connectorId) {

    Endpoint::where('connector_id', $connectorId)->delete();
    Connector::where('id', $connectorId)->delete();

    return response()->json(Connectors::all());
})->middleware(['auth', 'verified'])->name('data')
    ->name('delete_connector');

Route::get('connectors', function () {

    return response()->json(Connectors::all());
})->middleware(['auth', 'verified'])->name('data')
    ->name('subpanel_relationship_fields');
