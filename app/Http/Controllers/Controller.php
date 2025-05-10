<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

abstract class Controller
{
    public function output($success, $data = null, $message = null) {
        return response()->json(['success' => $success, 'data' => $data, 'message' => $message]);
    }
    
    public function callApi($uri, $method = "GET", $data = null)
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
                // dd($data);
                $response = Http::withHeaders($headers)->send($method, $url, [
                    'body' => $data,
                ]);
            }
            
            return ['success'=> $response->successful(), 'data'=> $response->json(), 'message' => $response->successful() ? null : $response->body()];
        } catch (\Throwable $th) {
            return ['success'=> false, 'data'=> null, 'message'=> $th->getMessage()];
        }
        
    }
}
