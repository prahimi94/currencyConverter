<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Http;
use App\Services\InfluxService;

class HttpMacroServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Http::macro('withInfluxLogging', function () {
            return Http::withMiddleware(function (callable $handler) {
                return function ($request, array $options) use ($handler) {
                    $start = microtime(true);

                    return $handler($request, $options)->then(function ($response) use ($request, $start) {
                        $duration = microtime(true) - $start;

                        app(InfluxService::class)->writeApiCallLog(
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
