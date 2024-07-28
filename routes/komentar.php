<?php

use App\Http\Controllers\Dashboard\KomentarController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KomentarController::class, 'index']);
Route::get('/get_data', [KomentarController::class, 'getData']);
Route::get('/{id}', [KomentarController::class, 'show']);
Route::post('/', [KomentarController::class, 'create']);
