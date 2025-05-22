<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\CurrencyServiceInterface;
use App\Services\Contracts\CallApiServiceInterface;
use App\Services\Contracts\ExceptionHandlerInterface;
use App\Services\CurrencyService;
use App\Services\CallApiService;
use App\Services\ExceptionHandlerService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CurrencyServiceInterface::class, CurrencyService::class);
        $this->app->bind(CallApiServiceInterface::class, CallApiService::class);
        $this->app->bind(ExceptionHandlerInterface::class, ExceptionHandlerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
