<?php

namespace App\Services;

use \App\Services\Contracts\CallApiServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class CallApiService implements CallApiServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function callApi(string $uri, string $method = "GET", ?string $data = null)
    {
        try {
            $apiKey = env('EXCHANGE_RATE_API_KEY');
            $apiUrl = env('EXCHANGE_RATE_API_URL');

            $url = $apiUrl . $uri;

            $headers = [
                'Authorization' => 'ApiKey ' . $apiKey,
                'Accept' => 'application/json',
            ];
            
            if ($method === 'GET') {
                $response = Http::withHeaders($headers)->get($url, $data);
            } else {
                $response = Http::withHeaders($headers)->send($method, $url, [
                    'body' => $data,
                ]);
            }

            if (!$response->successful()) {
                throw new RequestException($response);
            }
            
            return $response->json();
        } catch (\Throwable $th) {
            throw new \RuntimeException('API call failed: ' . $th->getMessage(), 0, $th);
        }
        
    }
}
