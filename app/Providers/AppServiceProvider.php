<?php

namespace App\Providers;

use App\Auth\CafeUserProvider;
use Illuminate\Support\Facades\Auth;
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
        Auth::provider('eloquent', function ($app, array $config) {
            return new CafeUserProvider($app['hash'], $config['model']);
        });
    }
}
