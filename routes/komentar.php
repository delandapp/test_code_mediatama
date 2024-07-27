<?php

use App\Http\Controllers\Dashboard\KomentarController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KomentarController::class, 'index']);
Route::get('/get_user', [KomentarController::class, 'getUsers']);
Route::get('/{id}', [KomentarController::class, 'show']);
Route::post('/tambah', [KomentarController::class, 'create']);
