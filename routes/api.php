<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyRestController;
use App\Http\Controllers\CurrencyGraphController;



Route::prefix('rest')->group(function () {
    Route::get('/currencies', [CurrencyRestController::class, 'currencies']);
    Route::post('/convert', [CurrencyRestController::class, 'convert']);
});

Route::prefix('graph')->group(function () {
    Route::post('/convert', [CurrencyGraphController::class, 'convert']);
});
