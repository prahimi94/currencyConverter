<?php

namespace App\Http\Controllers;

use App\Services\Contracts\CurrencyServiceInterface;
use App\Services\Contracts\CallApiServiceInterface;
use App\Http\Requests\ConvertCurrencyRequest;
use Illuminate\Support\Facades\Cache;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use App\Services\ExceptionHandlerService;
use Illuminate\Validation\ValidationException;


class CurrencyRestController extends Controller
{
    public function __construct(
        protected CurrencyServiceInterface $currencyService, 
        protected CallApiServiceInterface $callApiService,
        protected ExceptionHandlerService $exceptionHandler
    ) {
    }
    
    public function currencies()
    {
        try{
            $cacheKey = "currencies";
            $cachedCurrencies = Cache::get($cacheKey);
            if ($cachedCurrencies) {
                return new ApiResponse(true, $cachedCurrencies);
            }

            $response = $this->callApiService->callApi('/rest/currencies');

            Cache::put($cacheKey, $response, 600);

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
            return $this->exceptionHandler->handle($th);
        }
    }

    private function getRates(){
        try {
            $cacheKey = "rates";
            $cachedRates = Cache::get($cacheKey);
            if ($cachedRates) {
                return $cachedRates;
            }

            $response = $this->callApiService->callApi('/rest/rates');

            Cache::put($cacheKey, $response, 600);

            return $response;
        } catch (\Throwable $th){
            throw($th);
        }
    }
    
}
