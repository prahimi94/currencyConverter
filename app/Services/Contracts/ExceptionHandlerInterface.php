<?php

namespace App\Services\Contracts;

use Throwable;
use App\Http\Responses\ApiResponse;

interface ExceptionHandlerInterface
{
    /**
     * calculate the currency conversion
     *
     * @param Throwable $th
     * @return ApiResponse
     */
    public function handle(Throwable $th): ApiResponse;
}
