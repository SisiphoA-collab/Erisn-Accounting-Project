<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// Protect customer routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/customers', [CustomerController::class, 'index']);
    Route::post('/customers', [CustomerController::class, 'store']);
    Route::put('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('customers' , App\Http\Controllers\CustomerController::class);
Route::apiResource('vendors' , App\Http\Controllers\VendorController::class);
Route::apiResource('invoices' , App\Http\Controllers\InvoiceController::class);
Route::apiResource('expenses' , App\Http\Controllers\ExpenseController::class);
Route::apiResource('payments' , App\Http\Controllers\PaymentController::class);
Route::post('/paystack/initialize', [App\Http\Controllers\PaymentController::class, 'initializePaystack']);
Route::get('/paystack/callback', [App\Http\Controllers\PaymentController::class, 'paystackCallback']);

