<?php

use App\Http\Controllers\Dashboard\DislikeController;
use Illuminate\Routing\Route;

Route::get('/dislike/user/{id}', [DislikeController::class, 'userDislike']);
Route::post('/dislike', [DislikeController::class, 'create']);
Route::post('/dislike/{id}', [DislikeController::class, 'update']);
Route::delete('/dislike/{id}', [DislikeController::class, 'destroy']);
