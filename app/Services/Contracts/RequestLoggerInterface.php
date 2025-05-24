<?php

namespace App\Services\Contracts;

interface RequestLoggerInterface
{
    public const HTTP_REQUESTS_LOG_KEY = 'httpRequests';
    public const API_CALL_REQUESTS_LOG_KEY = 'apiCallRequests';

    public function logRequest($measurement, $method, $url, $requestData, $responseData, $duration):void;
}