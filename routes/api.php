<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CurrencyRestController;
use App\Http\Controllers\CurrencyGraphqlController;


Route::prefix('rest')->group(function () {
    Route::get('/currencies', [CurrencyRestController::class, 'currencies']);
    Route::post('/convert', [CurrencyRestController::class, 'convert']);
});

Route::prefix('graphql')->group(function () {
    Route::get('/currencies', [CurrencyGraphqlController::class, 'currencies']);
    Route::post('/convert', [CurrencyGraphqlController::class, 'convert']);
});
