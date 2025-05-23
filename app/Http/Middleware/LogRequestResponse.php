<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\InfluxService;
use Illuminate\Support\Facades\Log;

class LogRequestResponse
{

    public function __construct(protected InfluxService $influx)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = microtime(true) - $start;

        $this->influx->writeRequestLog(
            $request->method(),
            $request->fullUrl(),
            $request->all(),
            $response->getContent(),
            $duration
        );

        return $response;
    }
}
