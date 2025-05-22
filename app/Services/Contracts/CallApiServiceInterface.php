<?php

namespace App\Services\Contracts;

interface CallApiServiceInterface
{
    public function callApi(string $uri, string $method = "GET", ?string $data = null);
}
