<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Webhook xử lý thanh toán tự động
Route::post('/payment/webhook', [\App\Http\Controllers\PaymentWebhookController::class, 'handle']);
