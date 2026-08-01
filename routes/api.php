<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('me', [UserController::class, 'me']);
    Route::match(['put', 'patch'], 'me', [UserController::class, 'update']);

    Route::get('dashboard', DashboardController::class);

    Route::apiResource('projects', ProjectController::class);

    Route::get('projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('projects/{project}/tasks', [TaskController::class, 'store']);

    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::match(['put', 'patch'], 'tasks/{task}', [TaskController::class, 'update']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);
});
