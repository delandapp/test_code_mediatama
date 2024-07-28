<?php

use App\Models\Materi\Materi;
use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::group([], base_path('routes/home.php'));
});


Route::get('/check-permission/{permission}', function ($permission) {
    return response()->json(['allowed' => auth()->user()->can($permission)]);
})->middleware('auth');
