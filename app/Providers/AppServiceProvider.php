<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\OpenFoodService;
use App\Repositories\OpenFoodRepository;
use App\Services\AuthService;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Registra o AuthService como um singleton no contêiner
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService(new OpenFoodRepository());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // Registro do OpenFoodService com o OpenFoodRepository
        $this->app->bind(OpenFoodService::class, function ($app) {
            return new OpenFoodService(new OpenFoodRepository());
        });
    }
}
