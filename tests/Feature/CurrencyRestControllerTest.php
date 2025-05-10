<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CurrencyRestControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_currencies_correct_result(): void
    {
        Http::fake([
            $apiUrl = env('EXCHANGE_RATE_API_URL') . '*' => Http::response([
                [
                    "code" => "AED",
                    "numeric_code" => "784",
                    "decimal_digits" => 2,
                    "name" => "United Arab Emirates dirham",
                    "active" => true
                ],
            ], 200)
        ]);

        $response = $this->getJson('/api/rest/currencies');

        // $this->assertTrue(true);
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        [
                            "code" => "AED",
                            "numeric_code" => "784",
                            "decimal_digits" => 2,
                            "name" => "United Arab Emirates dirham",
                            "active" => true
                        ]
                    ],
                    'message' => null
                ]);
    }

    public function test_rates_correct_result(): void
    {
        Http::fake([
            $apiUrl = env('EXCHANGE_RATE_API_URL') . '*' => Http::response([
                [
                    "base_currency" => "EUR",
                    "quote_currency" => "AED",
                    "quote" => 4.171335,
                    "date" => "2025-05-05"  
                ],
                [
                    "base_currency" => "EUR",
                    "quote_currency" => "USD",
                    "quote" => 1.1343,
                    "date" => "2025-05-05"
                ],
                [
                    "base_currency" => "EUR",
                    "quote_currency" => "AFN",
                    "quote" => 80.566756,
                    "date" => "2025-05-05"
                ],
            ], 200)
        ]);
        $this->withoutMiddleware(\App\Http\Middleware\CsrfTokenHandler::class);

        $response = $this->postJson('/api/rest/convert', [
            'from' => 'EUR',
            'to' => 'USD',
            'amount' => 100
        ]);

        // $this->assertTrue(true);
        $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => 113.43,
                    'message' => null
                ]);
    }

    public function test_convert_validation_error(): void
    {
        Http::fake([
            $apiUrl = env('EXCHANGE_RATE_API_URL') . '*' => Http::response([
                [
                    "base_currency" => "EUR",
                    "quote_currency" => "AED",
                    "quote" => 4.171335,
                    "date" => "2025-05-05"  
                ],
                [
                    "base_currency" => "EUR",
                    "quote_currency" => "USD",
                    "quote" => 1.1343,
                    "date" => "2025-05-05"
                ],
                [
                    "base_currency" => "EUR",
                    "quote_currency" => "AFN",
                    "quote" => 80.566756,
                    "date" => "2025-05-05"
                ],
            ], 200)
        ]);
        $this->withoutMiddleware(\App\Http\Middleware\CsrfTokenHandler::class);

        $response = $this->postJson('/api/rest/convert', [
            'from' => 'EUR',
            'to' => 'US',
            'amount' => 100
        ]);

        // $this->assertTrue(true);
        $response->assertStatus(422)
                ->assertJson([
                    'message' => "Destination currency must be 3 characters long.",
                    "errors" => [
                        "to" => [
                            "Destination currency must be 3 characters long."
                        ]
                    ]
                ]);
    }
}
