<?php

namespace App\Services\Contracts;
use Throwable;
use Illuminate\Http\Request;

interface ExceptionLoggerInterface
{
    public function logException(Throwable $th, ?Request $request = null): void;
}