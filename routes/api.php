<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeApiController;

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

Route::middleware('api')->group(function () {
    Route::resource('employeeapi', EmployeeApiController::class);
    Route::get('historyapi', [EmployeeApiController::class, 'get_history_data']);
    Route::post('deleteapi', [EmployeeApiController::class, 'delete']);
    // Route::get('delete_historyapi',[EmployeeApiController::class,'delete_history']);
    Route::get('update/{id}', [EmployeeApiController::class, 'edit']);
    Route::post('update/{id}', [EmployeeApiController::class, 'update']);
    Route::get('test/{id}', [EmployeeApiController::class, 'test_send_mail']);
    Route::post('send_all', [EmployeeApiController::class, 'sendMailToAll']);
});
