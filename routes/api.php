<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TodosController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/todos', [TodosController::class, 'index']);
    Route::post('/todos', [TodosController::class, 'store']);
    Route::put('/todos/{id}', [TodosController::class, 'update']);
    Route::delete('/todos/{id}', [TodosController::class, 'destroy']);
});
