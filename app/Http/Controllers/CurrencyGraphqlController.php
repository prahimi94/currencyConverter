<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ConvertRequest;
use App\Http\Requests\ConvertCurrencyRequest;
use Illuminate\Support\Facades\Cache;

class CurrencyGraphqlController extends CurrencyController
{
    public function currencies()
    {
        $cacheKey = "currencies";
        $cachedCurrencies = Cache::get($cacheKey);
        if ($cachedCurrencies) {
            return $this->output(true, $cachedCurrencies);
        }

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
        $currencies = $response['data'];
        if (isset($currencies['data']['currencies'])) {
            $currencies = $currencies['data']['currencies'];
        } else {
            return $this->output(false, null, 'No data found');
        }

        Cache::put($cacheKey, $currencies, 600);

        return $this->output($response['success'], $currencies, $response['message']);
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
        
        $convertedAmount = $this->calculate($rates, $from, $to, $amount);
        if ($convertedAmount === null) {
            return $this->output(false, null, 'Conversion failed');
        }
        
        return $this->output(true, $convertedAmount);
    }

    private function getRates(){
        $cacheKey = "rates";
        $cachedRates = Cache::get($cacheKey);
        if ($cachedRates) {
            return ['success'=> true, 'data'=> $cachedRates, 'message'=> ''];
        }

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
        $rates = $response['data'];
        if (isset($rates['data']['latest'])) {
            $rates = $rates['data']['latest'];
        } else {
            return $this->output(false, null, 'No data found');
        }
        
        Cache::put($cacheKey, $rates, 600);

        return ['success'=> $response['success'], 'data'=> $rates, 'message'=> $response['message']];
    }
}
