<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CsrfTokenHandler
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('POST') && !$request->hasHeader('X-CSRF-TOKEN')) {
            return response()->json(['error' => 'CSRF token not found'], 400);
        }

        return $next($request);
    }
}

