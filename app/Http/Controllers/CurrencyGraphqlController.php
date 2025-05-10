<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ConvertRequest;
use App\Http\Requests\ConvertCurrencyRequest;

class CurrencyGraphqlController extends CurrencyController
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
        
        $convertedAmount = $this->calculate($rates, $from, $to, $amount);
        if ($convertedAmount === null) {
            return $this->output(false, null, 'Conversion failed');
        }
        
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
