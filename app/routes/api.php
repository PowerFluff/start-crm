<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\DealController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('contacts', ContactController::class);
    Route::apiResource('deals', DealController::class);
    Route::apiResource('tasks', TaskController::class);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');