<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyRestController extends Controller
{
    public function currencies()
    {
        $response = $this->callApi('/currencies');
        return response()->json($response);
    }

    public function convert(Request $request)
    {
        $from = $request->input('from');
        $to = $request->input('to');
        if($from == $to) {
            //todo
        }
        $amount = $request->input('amount');
        $rates = $this->getRates();
        $rateFound = false;
        $rateValue = 0;
        $convertedAmount = 0;
        foreach($rates as $rate) {
            if ($rate['base_currency'] == $from && $rate['quote_currency'] == $to
            ) {
                $rateValue = $rate['quote'];
                $rateFound = true;
                break;
            } else if ($rate['base_currency'] == $to && $rate['quote_currency'] == $from) {
                $rateValue = 1 / $rate['quote'];
                $rateFound = true;
                break;
            }
        }
        if($rateFound) {
            $convertedAmount = $amount * $rateValue;
        } else {
            foreach($rates as $rate) {
                if ($rate['base_currency'] == 'EUR' && $rate['quote_currency'] == $from
                ) {
                    $rateValueA = 1 / $rate['quote'];
                } 
                if ($rate['base_currency'] == 'EUR' && $rate['quote_currency'] == $to) {
                    $rateValueB = $rate['quote'];
                }
            }


            $convertedAmount = $amount * $rateValueA * $rateValueB;
        }

        dd($convertedAmount);
        die('here');
        return response()->json($convertedAmount);
    }

    private function getRates(){
        $response = $this->callApi('/rates');
        return $response;
    }

    private function callApi($uri, $method = "GET", $data = null)
    {
        $apiKey = env('EXCHANGE_RATE_API_KEY');
        $apiUrl = env('EXCHANGE_RATE_API_REST_URL');
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
        return json_decode($response, true);
    }
}
