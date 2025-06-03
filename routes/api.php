<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

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

