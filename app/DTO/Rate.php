<?php

namespace App\DTO;

class Rate
{
    public function __construct(
        public readonly string $base_currency,
        public readonly string $quote_currency,
        public readonly float $quote,
        public readonly string $date,
    ) {}
}
