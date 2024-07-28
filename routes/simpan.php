<?php

use App\Http\Controllers\Dashboard\SimpanController;
use Illuminate\Support\Facades\Route;

Route::get('/user/{id}', [SimpanController::class, 'userSimpan']);
Route::post('/', [SimpanController::class, 'create']);
Route::post('/{id}', [SimpanController::class, 'update']);
Route::delete('/{id}', [SimpanController::class, 'destroy']);
