<?php

use App\Http\Controllers\Api\CRMController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/crm', [CRMController::class, 'index']);
    Route::get('/crm/search', [CRMController::class, 'search']);
    Route::get('/crm/{id}', [CRMController::class, 'show']);
    Route::put('/crm/{id}', [CRMController::class, 'updateoradd']);
    Route::delete('/crm/{id}/{type}', [CRMController::class, 'destroy']);
});



Route::post('/login', [AuthController::class, 'login']);
