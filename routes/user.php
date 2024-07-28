<?php

use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'index']);
Route::get('/get_user', [UserController::class, 'getUsers']);
Route::get('/{id}', [UserController::class, 'show']);
Route::post('/tambah', [UserController::class, 'create']);
Route::post('/edit/{id}', [UserController::class, 'update']);
Route::post('/delete/{id}', [UserController::class, 'destroy']);
