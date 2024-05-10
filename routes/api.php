<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/todos', [TodoController::class, 'createTodo']);
    Route::get('/todos', [TodoController::class, 'getAllTodosForUser']);
    Route::get('/todos/{todo}', [TodoController::class, 'getTodo']);
    Route::patch('/todos/{todo}', [TodoController::class, 'updateTodo']);
    Route::delete('/todos/{todo}', [TodoController::class, 'deleteTodo']);

    Route::post('/todos/{todo}/tasks', [TaskController::class, 'createTask']);
    Route::get('/todos/{todo}/tasks', [TaskController::class, 'getAllTaskForATodo']);
    Route::get('/tasks/{task}', [TaskController::class, 'getTask']);
    Route::patch('/tasks/{task}', [TaskController::class, 'updateTask']);
    Route::delete('/tasks/{task}', [TaskController::class, 'deleteTask']);

    Route::post('/logout', [AuthController::class, 'logout']);

});
