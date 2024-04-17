<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;

Route::controller(MainController::class)->group(function () {
    Route::get('/pf/products/{productId}', 'getPrintFulProductById');
    Route::post('/pf/orders/create', 'printFulCreateOrder');
});
