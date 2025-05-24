<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\CsrfTokenHandler::class);
        $middleware->append(\App\Http\Middleware\CspHandler::class); //for Content Security Policy (CSP)
        $middleware->append(\App\Http\Middleware\LogRequestResponse::class); //for logging requests and responses
    })
    ->withProviders([
        \App\Providers\HttpMacroServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function (Throwable $e, Request $request) {
            app(\App\Services\ExceptionHandlerService::class)->report($e, $request);
            
            $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : JsonResponse::HTTP_INTERNAL_SERVER_ERROR; //500
            
            return new ApiResponse(false, null, $e->getMessage(), $status);
        });
    })->create();
