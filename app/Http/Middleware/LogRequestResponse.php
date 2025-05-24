<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\Contracts\RequestLoggerInterface;

class LogRequestResponse
{

    public function __construct(protected RequestLoggerInterface $logger)
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

        $this->logger->logRequest(
            $this->logger::HTTP_REQUESTS_LOG_KEY,
            $request->method(),
            $request->fullUrl(),
            $request->all(),
            $response->getContent(),
            $duration
        );

        return $response;
    }
}
