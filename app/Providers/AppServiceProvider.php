<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contracts\CurrencyServiceInterface;
use App\Services\Contracts\CallApiServiceInterface;
use App\Services\Contracts\ExceptionHandlerInterface;
use App\Services\Contracts\ExceptionLoggerInterface;
use App\Services\Contracts\RequestLoggerInterface;
use App\Services\CurrencyService;
use App\Services\CallApiService;
use App\Services\ExceptionHandlerService;
use App\Services\ExceptionFileLoggerService;
use App\Services\RequestInfluxLoggerService;

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
        $this->app->bind(ExceptionLoggerInterface::class, ExceptionFileLoggerService::class);
        $this->app->bind(RequestLoggerInterface::class, RequestInfluxLoggerService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
