<?php

use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PizzaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public endpoints
|--------------------------------------------------------------------------
*/

Route::apiResource('pizzas', PizzaController::class)->only(['index', 'show']);

/*
|--------------------------------------------------------------------------
| Authenticated endpoints
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard CRUDs
    Route::apiResource('ingredients', IngredientController::class)->except(['show']);
    Route::apiResource('pizzas', PizzaController::class)->only(['store', 'update', 'destroy']);

    // Orders
    Route::apiResource('orders', OrderController::class)->only(['index', 'store']);
});
