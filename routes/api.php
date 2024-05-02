<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::post('/users/register', [AuthController::class, 'register']);
Route::post('/users/login', [AuthController::class, 'login']);

Route::middleware([Authenticate::class])->group(function () {
    Route::controller(MainController::class)->group(function () {
        Route::get('/pf/store/{storeId}/products', 'getPrintFulStoreSyncProducts');
        Route::get('/pf/store/{storeId}/products/{productId}', 'getPrintFulStoreSyncProductById');
        Route::get('/pf/store/{storeId}/products/{productId}/sync-variants', 'getSyncProductVariantsById');
        Route::post('/pf/orders/create', 'printFulCreateOrder');
        Route::post('/pf/orders/confirmation', 'printFulConfirmOrder');
    });
});

Route::post('/pf/webhook', [WebhookController::class, 'webhookHandler']);
