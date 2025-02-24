<?php

use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::prefix('user')->group(base_path('routes/user.php'));
    Route::prefix('video')->group(base_path('routes/video.php'));
    Route::prefix('request')->group(base_path('routes/request_video.php'));
    Route::prefix('user-video')->group(base_path('routes/user_video.php'));
    Route::prefix('komentar')->group(base_path('routes/komentar.php'));
});
Route::group([], base_path('routes/auth.php'));
