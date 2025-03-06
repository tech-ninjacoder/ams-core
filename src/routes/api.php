<?php

use App\Http\Controllers\Tenant\Attendance\AttendanceUpdateController;
use App\Http\Controllers\Tenant\Employee\AttendanceController;
use App\Http\Controllers\Tenant\Employee\HelmetController;
use Illuminate\Http\Request;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('app/catch',[AttendanceController::class,'fetch'])
    ->name('employees.update');
Route::post('app/vtsalert',[HelmetController::class,'vtsalert'])
    ->name('vts.alert');
