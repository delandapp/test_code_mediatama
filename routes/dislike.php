<?php

use App\Http\Controllers\Dashboard\DislikeController;
use Illuminate\Support\Facades\Route;

Route::get('/user/{id}', [DislikeController::class, 'userDislike']);
Route::get('/get_data/{id}', [DislikeController::class, 'getData']);
Route::post('/', [DislikeController::class, 'handleDislike']);
Route::post('/{id}', [DislikeController::class, 'update']);
Route::delete('/{id}', [DislikeController::class, 'destroy']);
