<?php

use App\Http\Controllers\Dashboard\UserVideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserVideoController::class, 'index']);
Route::get('/request', [UserVideoController::class, 'requestVideo']);
Route::get('/get_data', [UserVideoController::class, 'getData']);
Route::get('/get_data/{query}', [UserVideoController::class, 'getDataQuery']);
Route::get('/lihat/{id}', [UserVideoController::class, 'lihatVideo']);
Route::get('/show/{id}', [UserVideoController::class, 'show']);
Route::post('/clear-cache/{id}', [UserVideoController::class, 'clearCache'])->name('clear.cache');
