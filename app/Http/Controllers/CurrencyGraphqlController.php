<?php

namespace App\Http\Controllers;

use App\Services\Contracts\CurrencyServiceInterface;
use App\Services\Contracts\CallApiServiceInterface;
use App\Http\Requests\ConvertCurrencyRequest;
use Illuminate\Support\Facades\Cache;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\ExceptionHandlerService;

class CurrencyGraphqlController extends Controller
{
    public function __construct(
        protected CurrencyServiceInterface $currencyService, 
        protected CallApiServiceInterface $callApiService,
        protected ExceptionHandlerService $exceptionHandler
    ) {}

    public function currencies()
    {
        try{
            $cacheKey = "currencies";
            $cachedCurrencies = Cache::get($cacheKey);
            if ($cachedCurrencies) {
                return new ApiResponse(true, $cachedCurrencies);
            }

            $query = json_encode([
                'query' => 'query {
                    currencies {
                        code
                        numeric_code: numericCode
                        decimal_digits: decimalDigits
                        name
                        active
                    }
                }'
            ]);
            $response = $this->callApiService->callApi('/graphql', 'POST', $query);
            
            $currencies = $response['data'];
            if (isset($currencies['currencies'])) {
                $currencies = $currencies['currencies'];
            } else {
                return new ApiResponse(false, null, 'No data found', JsonResponse::HTTP_BAD_GATEWAY);
            }

            Cache::put($cacheKey, $currencies, 600);

            return new ApiResponse(true, $currencies, '');
        } catch (\Throwable $th) {
            return $this->exceptionHandler->handle($th);
        }
    }

    public function convert(ConvertCurrencyRequest $request)
    {
        try {
            $data = $request->validated();
            $from = $data['from'];
            $to = $data['to'];
            $amount = $data['amount'];

            $ratesRes = $this->getRates();        
            $convertedAmount = $this->currencyService->calculate($ratesRes, $from, $to, $amount);

            return new ApiResponse(true, $convertedAmount);
        } catch (\Throwable $th) {
            return $this->exceptionHandler->handle($th);
        }
    }

    private function getRates(){
        try{
            $cacheKey = "rates";
            $cachedRates = Cache::get($cacheKey);
            if ($cachedRates) {
                return $cachedRates;
            }

            $query = json_encode([
                'query' => 'query {
                    latest {
                        date
                        base_currency: baseCurrency
                        quote_currency: quoteCurrency
                        quote
                    }
                }'
            ]);
            $response = $this->callApiService->callApi('/graphql', 'POST', $query);
            
            $rates = $response['data'];
            if (isset($rates['latest'])) {
                $rates = $rates['latest'];
            } else {
                throw new \Exception('No data found');
            }

            Cache::put($cacheKey, $rates, 600);

            return $rates;
        } catch (\Throwable $th) {
            throw($th);
        }
    }
}
