<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('pdf_preview/{id}',[EmployeeController::class,'getPDF']);
Route::get('/history',[EmployeeController::class,'get_history_data']);
Route::get('edit/{id}', [EmployeeController::class, 'edit']);

Route::get('test',[EmployeeController::class, 'test']);
Route::post('test',[EmployeeController::class, 'upload_excel']);
Route::get('test/{id}',[EmployeeController::class, 'send_mail']);