<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceMailController;
use App\Http\Controllers\StipendController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BankingController;

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

Route::get('/banking', [BankingController::class, 'index']); // Public access for dashboard data

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('customers', App\Http\Controllers\CustomerController::class);
    Route::apiResource('vendors', App\Http\Controllers\VendorController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::apiResource('stipends', StipendController::class);
    Route::apiResource('dashboard', DashboardController::class);
    Route::apiResource('expenses', App\Http\Controllers\ExpenseController::class);
    Route::apiResource('payments', App\Http\Controllers\PaymentController::class);
    Route::apiResource('accounts', App\Http\Controllers\AccountController::class);
    Route::apiResource('reports', ReportController::class);
    Route::apiResource('transaction', App\Http\Controllers\TransactionController::class);

    Route::post('/paystack/initialize', [App\Http\Controllers\PaymentController::class, 'initializePaystack']);
    Route::get('/paystack/callback', [App\Http\Controllers\PaymentController::class, 'paystackCallback']);
    Route::get('reports/profit-loss', [ReportController::class, 'profitLoss']);
    Route::get('/reports/balance-sheet/pdf', [ReportController::class, 'balanceSheetPdf']);
    Route::get('reports/balance-sheet', [ReportController::class, 'balanceSheet']);

    // Invoices API routes
    Route::get('/invoice-stats', [InvoiceController::class, 'getInvoiceStats']);
    Route::post('/invoice/download-pdf', [InvoiceController::class, 'downloadPdf']);
    Route::post('/send-invoice', [InvoiceMailController::class, 'sendEmail']);

    // Stipends receipt upload API routes
    Route::post('stipends/{id}/upload', [StipendController::class, 'uploadReceipt']);
    Route::post('stipends/import', [StipendController::class, 'import'])->name('stipends.import');

    // Banking API routes
    Route::post('/banking/accounts', [BankingController::class, 'storeAccount']);
    Route::put('/banking/accounts/{id}', [BankingController::class, 'updateAccount']);
    Route::delete('/banking/accounts/{id}', [BankingController::class, 'destroyAccount']);
    Route::post('/banking/transactions', [BankingController::class, 'storeTransaction']);
    Route::put('/banking/transactions/{id}', [BankingController::class, 'updateTransaction']);
    Route::delete('/banking/transactions/{id}', [BankingController::class, 'destroyTransaction']);
    Route::post('/banking/reconciliations', [BankingController::class, 'storeReconciliation']);
    Route::put('/banking/reconciliations/{id}', [BankingController::class, 'updateReconciliation']);
    Route::delete('/banking/reconciliations/{id}', [BankingController::class, 'destroyReconciliation']);
    Route::post('/banking/import', [BankingController::class, 'import']);
});