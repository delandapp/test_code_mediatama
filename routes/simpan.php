<?php

use App\Http\Controllers\Dashboard\SimpanController;
use Illuminate\Routing\Route;

Route::get('/simpan/user/{id}', [SimpanController::class, 'userSimpan']);
Route::post('/simpan', [SimpanController::class, 'create']);
Route::post('/simpan/{id}', [SimpanController::class, 'update']);
Route::delete('/simpan/{id}', [SimpanController::class, 'destroy']);
