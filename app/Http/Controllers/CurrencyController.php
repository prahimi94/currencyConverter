<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    public function calculate($rates, $from, $to, $amount) {
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
        return $convertedAmount;
    }
}
