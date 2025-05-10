<?php

namespace App\Http\Controllers;

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

            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => $method,
                CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                'Authorization: ApiKey ' . $apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
                ),
            ));
            
            $response = curl_exec($curl);

            curl_close($curl);
            
            return ['success'=> true, 'data'=> json_decode($response, true), 'message'=> null];
        } catch (\Throwable $th) {
            return ['success'=> false, 'data'=> null, 'message'=> $th->getMessage()];
        }
        
    }
}
