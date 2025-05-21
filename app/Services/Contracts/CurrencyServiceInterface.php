<?php

namespace App\Services\Contracts;

interface CurrencyServiceInterface
{
    /**
     * calculate the currency conversion
     *
     * @param array $rates
     * @param string $from
     * @param string $to
     * @param float $amount
     * @return float
     */
    public function calculate(array $rates, string $from, string $to, float $amount): float;
}
