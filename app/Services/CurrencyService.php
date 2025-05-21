<?php

namespace App\Services;
use App\Services\Contracts\CurrencyServiceInterface;

class CurrencyService implements CurrencyServiceInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function calculate(array $rates, string $from, string $to, float $amount): float{
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
        $rateValueA = null;
        $rateValueB = null;

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
                if ($rateValueA !== null && $rateValueB !== null) {
                    break;
                }
            }

            if ($rateValueA == null || $rateValueB == null) {
                throw new \InvalidArgumentException('Conversion rate not found for the given currencies.');
            }

            $convertedAmount = $amount * $rateValueA * $rateValueB;
        }

        $convertedAmount = round($convertedAmount, 2);
        return $convertedAmount;
    }
}
