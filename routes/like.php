<?php

use App\Http\Controllers\Dashboard\LikeController;
use Illuminate\Routing\Route;

Route::get('/like/user/{id}', [LikeController::class, 'userLike']);
Route::post('/like', [LikeController::class, 'create']);
Route::post('/like/{id}', [LikeController::class, 'update']);
Route::delete('/like/{id}', [LikeController::class, 'destroy']);
