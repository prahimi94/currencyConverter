<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConvertCurrencyRequest;

class CurrencyGraphqlController extends controller
{
    public function currencies()
    {
        $query = json_encode([
            'query' => 'query {
                currencies {
                    code
                    numeric_code: numericCode
                    decimal_digits: decimalDigits
                    name
                    active
                }
            }'
        ]);
        $response = $this->callApi('/graphql', 'POST', $query);
        if($response['success'] == false) {
            return $this->output(false, null, $response['message']);
        }
        if (isset($response['data']['currencies'])) {
            $response = $response['data']['currencies'];
        } else {
            return $this->output(false, null, 'No data found');
        }
        return $this->output($response['success'], $response['data'], $response['message']);
    }

    public function convert(ConvertCurrencyRequest $request)
    {
        $data = $request->validated();
        $from = $data['from'];
        $to = $data['to'];
        if($from == $to) {
            return $this->output(false, null, 'from and to currencies are the same');
        }
        $amount = $data['amount'];
        $ratesRes = $this->getRates();
        if($ratesRes['success'] == false) {
            return $this->output(false, null, $ratesRes['message']);
        }
        $rates = $ratesRes['data'];

        if (isset($rates['data']['latest'])) {
            $rates = $rates['data']['latest'];
        } else {
            return $this->output(false, null, 'No data found');
        }
        
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

        $convertedAmount = round($convertedAmount, 2);
        
        return $this->output(true, $convertedAmount);
    }

    private function getRates(){
        $query = json_encode([
            'query' => 'query {
                latest {
                    date
                    base_currency: baseCurrency
                    quote_currency: quoteCurrency
                    quote
                }
            }'
        ]);
        $response = $this->callApi('/graphql', 'POST', $query);
        if($response['success'] == false) {
            return $this->output(false, null, $response['message']);
        }
        return ['success'=> $response['success'], 'data'=> $response['data'], 'message'=> $response['message']];
    }
}
