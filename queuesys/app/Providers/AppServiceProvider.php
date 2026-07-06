<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

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
        // URL::forceScheme('https');
        $offices = [
            'Business Office',
            'Library',
            'Student Affairs',
            'Academic Affairs',
        ];

        // Make $offices available in all views
        View::share('offices', $offices);
    }
}
