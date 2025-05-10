<?php

namespace Tests\Unit;

use App\Http\Requests\ConvertCurrencyRequest;
// use PHPUnit\Framework\TestCase;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class ConvertCurrencyRequestTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_valid_data_passes_validation()
    {
        $data = [
            'from' => 'USD',
            'to' => 'EUR',
            'amount' => 100,
        ];

        $request = new ConvertCurrencyRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertTrue($validator->passes());
    }

    public function test_invalid_data_fails_validation()
    {
        $data = [
            'from' => 'US', // invalid: too short
            'to' => '',     // invalid: required
            'amount' => 'khj', // invalid: numeric
        ];

        $request = new ConvertCurrencyRequest();
        $validator = Validator::make($data, $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('from', $validator->errors()->toArray());
        $this->assertArrayHasKey('to', $validator->errors()->toArray());
        $this->assertArrayHasKey('amount', $validator->errors()->toArray());
    }
}
