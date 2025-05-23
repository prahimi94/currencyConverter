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

    public function calculate(array $rates, string $from, string $to, float $amount): float
    {
        foreach ($rates as $rate) {
            if ($rate->base_currency == $from && $rate->quote_currency == $to) {
                return $this->roundResult($amount * $rate->quote);
            } else if ($rate->base_currency == $to && $rate->quote_currency == $from) {
                return $this->roundResult($amount / $rate->quote);
            }
        }

        $rateValueA = null;
        $rateValueB = null;
        foreach ($rates as $rate) {
            if ($rate->base_currency == 'EUR' && $rate->quote_currency == $from) {
                $rateValueA = 1 / $rate->quote;
            }
            if ($rate->base_currency == 'EUR' && $rate->quote_currency == $to) {
                $rateValueB = $rate->quote;
            }
            if ($rateValueA !== null && $rateValueB !== null) {
                break;
            }
        }

        if ($rateValueA == null) {
            throw new \InvalidArgumentException('Conversion rate not found for the currency: '. $from);
        } else if ($rateValueB == null) {
            throw new \InvalidArgumentException('Conversion rate not found for the currency: '. $to);
        }

        $convertedAmount = $amount * $rateValueA * $rateValueB;
        return $this->roundResult($convertedAmount);
    }

    private function roundResult(float $input): float
    {
        return round($input, 2);
    }
}
