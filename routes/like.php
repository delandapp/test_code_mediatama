<?php

use App\Http\Controllers\Dashboard\LikeController;
use Illuminate\Support\Facades\Route;

Route::get('/user/{id}', [LikeController::class, 'userLike']);
Route::post('/', [LikeController::class, 'handleLike']);
Route::get('/get_data/{id}', [LikeController::class, 'getData']);
Route::post('/{id}', [LikeController::class, 'update']);
Route::delete('/{id}', [LikeController::class, 'destroy']);
