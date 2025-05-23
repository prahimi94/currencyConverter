<?php

namespace App\Http\Controllers;

use App\DTO\Currency;
use App\DTO\Rate;
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
            $response = $this->callApiService->callApi(uri: '/graphql', method: 'POST', data: $query, dtoClass: Currency::class, dataPath: 'data.currencies');
            
            Cache::put($cacheKey, $response, config('cache.ttl'));

            return new ApiResponse(true, $response, '');
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
            return $this->exceptionHandler->handle($th, $request);
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
            $response = $this->callApiService->callApi(uri: '/graphql', method: 'POST', data: $query, dtoClass: Rate::class, dataPath: 'data.latest');
            
            Cache::put($cacheKey, $response, config('cache.ttl'));

            return $response;
        } catch (\Throwable $th) {
            throw($th);
        }
    }
}
