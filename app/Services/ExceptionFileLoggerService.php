<?php

namespace App\Services;

use App\Services\Contracts\ExceptionLoggerInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Throwable;

class ExceptionFileLoggerService implements ExceptionLoggerInterface
{
    public function logException(Throwable $th, ?Request $request = null): void
    {
        Log::channel('exceptionLogs')->error('Exception occurred', [
            'message' => $th->getMessage(),
            'exception' => get_class($th),
            'url' => optional($request)->fullUrl(),
            'method' => optional($request)->method(),
            'input' => optional($request)->all(),
            'file' => $th->getFile(),
            'line' => $th->getLine(),
        ]);
    }
}
