<?php

namespace Tests\Unit;

use App\Http\Controllers\CurrencyController;
use PHPUnit\Framework\TestCase;

class CurrencyConverterCalculationTest extends TestCase
{
    private array $rates;
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the rates array
        $this->rates = [
            ['base_currency' => 'EUR', 'quote_currency' => 'USD', 'quote' => 1.125200, 'date' => '2025-05-10'],
            ['base_currency' => 'EUR', 'quote_currency' => 'GBP', 'quote' => 0.847875, 'date' => '2025-05-10'],
            ['base_currency' => 'EUR', 'quote_currency' => 'AED', 'quote' => 4.131207, 'date' => '2025-05-10'],
        ];
    }

    /**
     * A basic unit test example.
     */
    public function test_calculate_currency_in_rates(): void
    {
        $currencyController = new CurrencyController();
        $rates = $this->rates;
        $from = 'EUR';
        $to = 'USD';
        $amount = 100;
        $result = $currencyController->calculate($rates, $from, $to, $amount);

        $this->assertEquals(112.52, $result);
    }


    public function test_calculate_currency_not_in_rates(): void
    {
        $currencyController = new CurrencyController();
        $rates = $this->rates;
        $from = 'USD';
        $to = 'GBP';
        $amount = 100;
        $result = $currencyController->calculate($rates, $from, $to, $amount);

        $this->assertEquals(75.35, $result);
    }

    public function test_calculate_zero_amount(): void
    {
        $currencyController = new CurrencyController();
        $rates = $this->rates;
        $from = 'USD';
        $to = 'GBP';
        $amount = 0;
        $result = $currencyController->calculate($rates, $from, $to, $amount);

        $this->assertEquals(0, $result);
    }

    public function test_calculate_minus_amount(): void
    {
        $currencyController = new CurrencyController();
        $rates = $this->rates;
        $from = 'USD';
        $to = 'GBP';
        $amount = -100;
        $result = $currencyController->calculate($rates, $from, $to, $amount);

        $this->assertEquals(-75.35, $result);
    }
}
