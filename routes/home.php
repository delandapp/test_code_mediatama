<?php

use Illuminate\Support\Facades\Route;

Route::group([],function () {
    Route::prefix('user')->group(base_path('routes/user.php'));
});
