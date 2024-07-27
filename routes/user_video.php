<?php

use App\Http\Controllers\Dashboard\UserVideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserVideoController::class, 'index']);
