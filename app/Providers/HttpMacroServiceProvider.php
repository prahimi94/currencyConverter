<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use App\Services\RequestInfluxLoggerService;
use App\Services\Contracts\RequestLoggerInterface;

class HttpMacroServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Http::macro('withRequestLogging', function () {
            $logger = app(RequestLoggerInterface::class); // Only resolve the logger once

            return Http::withMiddleware(function (callable $handler) use ($logger) {
                return function ($request, array $options) use ($handler, $logger) {
                    $start = microtime(true);

                    return $handler($request, $options)->then(function ($response) use ($request, $start, $logger) {
                        $duration = microtime(true) - $start;

                        $logger->logRequest(
                            $logger::API_CALL_REQUESTS_LOG_KEY,
                            $request->getMethod(),
                            (string) $request->getUri(),
                            $request->getBody()->getContents(),
                            (string) $response->getBody(),
                            $duration
                        );

                        return $response;
                    });
                };
            });
        });
    }
}
