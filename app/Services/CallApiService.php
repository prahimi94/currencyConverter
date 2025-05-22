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

    public function callApi(string $uri, string $method = "GET", ?string $data = null, ?string $dtoClass = null, ?string $dataPath = null)
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
            
            $responseData = $response->json();

            if ($dataPath) {
                foreach (explode('.', $dataPath) as $key) {
                    if (isset($responseData[$key])) {
                        $responseData = $responseData[$key];
                    } else {
                        throw new \RuntimeException("Key '{$key}' not found in response.");
                    }
                }
            }
            
            if ($dtoClass) {
                if(is_array(($responseData))) {
                    return array_map(fn ($data) => new $dtoClass(...$data), $responseData);
                } else {
                    return new $dtoClass(...$responseData);
                }
            }

            return $responseData;
        } catch (\Throwable $th) {
            throw new \RuntimeException('API call failed: ' . $th->getMessage(), 0, $th);
        }
        
    }
}
