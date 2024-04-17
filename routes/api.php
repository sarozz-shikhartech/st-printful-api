<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

    Route::post('/users/register', [AuthController::class, 'register']);
    Route::post('/users/login', [AuthController::class, 'login']);

Route::middleware([Authenticate::class])->group(function () {
    Route::controller(MainController::class)->group(function () {
        Route::get('/pf/products/{productId}', 'getPrintFulProductById');
        Route::post('/pf/orders/create', 'printFulCreateOrder');
    });
});
