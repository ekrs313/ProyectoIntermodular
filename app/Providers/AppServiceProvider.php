<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // <-- 1. AÑADE ESTA LÍNEA AQUÍ

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
        // 2. AÑADE ESTA LÍNEA AQUÍ
        Schema::defaultStringLength(191); 
    }
}