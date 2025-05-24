<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;

class ApiResponse implements Responsable
{
    protected bool $success;
    protected mixed $data;
    protected ?string $message;
    protected ?int $httpStatusCode;

    public function __construct(bool $success, mixed $data = null, ?string $message = null, ?int $httpStatusCode = null)
    {
        $this->success = $success;
        $this->data = $data;
        $this->message = $message;
        $this->httpStatusCode = $httpStatusCode;
    }

    public function toResponse($request)
    {
        return response()->json(['success' => $this->success, 'data' => $this->data, 'message' => $this->message], 
            $this->httpStatusCode ?? ($this->success ? JsonResponse::HTTP_OK : JsonResponse::HTTP_BAD_REQUEST));
    }
}
