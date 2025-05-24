<?php

namespace App\DTO;

class Currency
{
    public function __construct(
        public readonly string $code,
        public readonly int $numeric_code,
        public readonly int $decimal_digits,
        public readonly string $name,
        public readonly bool $active
    ) {}
}
