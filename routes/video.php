<?php

use App\Http\Controllers\Dashboard\MateriController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MateriController::class, 'index']);
Route::get('/get_video', [MateriController::class, 'getVideo']);
Route::get('/{id}', [MateriController::class, 'show']);
Route::post('/tambah', [MateriController::class, 'create']);
