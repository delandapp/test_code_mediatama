<?php

namespace App\Providers;

use App\Models\Materi\Materi;
use App\Observers\MateriObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Materi::observe(MateriObserver::class);
    }
}
