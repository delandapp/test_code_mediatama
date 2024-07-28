<?php

use App\Http\Controllers\Dashboard\KomentarController;
use Illuminate\Support\Facades\Route;

Route::get('/', [KomentarController::class, 'index']);
Route::get('/get_data/{id}', [KomentarController::class, 'getData']);
Route::get('/show/{id}', [KomentarController::class, 'show']);
Route::post('/update/{id}', [KomentarController::class, 'update']);
Route::get('/{id}', [KomentarController::class, 'show']);
Route::post('/', [KomentarController::class, 'create']);
Route::post('/delete/{id}', [KomentarController::class, 'destroy']);
