<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ConvertRequest;
use App\Http\Requests\ConvertCurrencyRequest;

class CurrencyRestController extends CurrencyController
{
    public function currencies()
    {
        $response = $this->callApi('/rest/currencies');
        if($response['success'] == false) {
            return $this->output(false, null, $response['message']);
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

        $convertedAmount = $this->calculate($rates, $from, $to, $amount);
        if ($convertedAmount === null) {
            return $this->output(false, null, 'Conversion failed');
        }

        return $this->output(true, $convertedAmount);
    }

    private function getRates(){
        $response = $this->callApi('/rest/rates');
        if($response['success'] == false) {
            return $this->output(false, null, $response['message']);
        }
        return ['success'=> $response['success'], 'data'=> $response['data'], 'message'=> $response['message']];
    }
    
}
