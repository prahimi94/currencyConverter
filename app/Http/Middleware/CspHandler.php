<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CspHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        if (app()->environment('local')) {
            $response->headers->set('Content-Security-Policy',
                "default-src 'self'; " .
                "script-src 'self' 'unsafe-inline' http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173; " .
                "script-src-elem 'self' 'unsafe-inline' http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173; " .
                "style-src 'self' 'unsafe-inline' http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173; " .
                "style-src-elem 'self' 'unsafe-inline' http://localhost:5173 http://127.0.0.1:5173 http://[::1]:5173; " .
                "connect-src 'self' ws://localhost:5173 ws://127.0.0.1:5173 http://localhost:5173 http://127.0.0.1:5173; " .
                "img-src 'self' data:;"
            );
        } else {
            $response->headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:;");
        }
        return $response;
    }
}
