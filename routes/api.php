<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('offices', \App\Http\Controllers\Office\OfficeController::class);
Route::apiResource('departments', \App\Http\Controllers\Departments\DepartmentController::class);
Route::apiResource('staff', \App\Http\Controllers\Staff\StaffController::class);
Route::apiResource('sanyojaks', \App\Http\Controllers\SanyojakController::class);
Route::apiResource('psts', \App\Http\Controllers\PstController::class);
