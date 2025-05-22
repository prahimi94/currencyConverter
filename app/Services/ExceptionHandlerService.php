<?php

namespace App\Services;

use App\Services\Contracts\ExceptionHandlerInterface;
use Throwable;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use GuzzleHttp\Exception\RequestException;

class ExceptionHandlerService implements ExceptionHandlerInterface
{
    public function handle(Throwable $th): ApiResponse
    {
        if ($th instanceof ValidationException) {
            return new ApiResponse(false, null, 'Validation error: ' . $th->getMessage(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        if ($th instanceof \RuntimeException) {
            return new ApiResponse(false, null, $th->getMessage(), JsonResponse::HTTP_INTERNAL_SERVER_ERROR); // 500
        }

        if ($th instanceof RequestException) {
            return new ApiResponse(false, null, 'External API error: ' . $th->getMessage(), JsonResponse::HTTP_BAD_GATEWAY); // 502
        }

        return new ApiResponse(false, null, $th->getMessage(), JsonResponse::HTTP_BAD_REQUEST); // 400
    }
}
