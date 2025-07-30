<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', ['pageTitle' => 'Dashboard']);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/chartofaccount', function () {
    return Inertia::render('ChartOfAccount', ['pageTitle' => 'Chart Of Account']);
})->middleware(['auth', 'verified'])->name('chartofaccount');

Route::get('/customers', function () {
    return Inertia::render('Customers', ['pageTitle' => 'Customers']);
})->middleware(['auth', 'verified'])->name('customers');

Route::get('/venders', function () {
    return Inertia::render('Vendors', ['pageTitle' => 'Vendors']);
})->middleware(['auth', 'verified'])->name('vendors');

Route::get('/invoices', function () {
    return Inertia::render('Invoices', ['pageTitle' => 'Invoices']);
})->middleware(['auth', 'verified'])->name('invoices');

Route::get('/payments', function () {
    return Inertia::render('Payments', ['pageTitle' => 'Payments']);
})->middleware(['auth', 'verified'])->name('payments');

Route::get('/expenses', function () {
    return Inertia::render('Expenses', ['pageTitle' => 'Expenses']);
})->middleware(['auth', 'verified'])->name('expenses');

Route::get('/stipends', function () {
    return Inertia::render('Stipends', ['pageTitle' => 'Stipends']);
})->middleware(['auth', 'verified'])->name('stipends');

Route::get('/bank', function () {
    return Inertia::render('Banking', ['pageTitle' => 'Banking']);
})->middleware(['auth', 'verified'])->name('bank');

Route::get('/reports', function () {
    return Inertia::render('Reports', ['pageTitle' => 'Reports']);
})->middleware(['auth', 'verified'])->name('reports');

Route::get('/settings', function () {
    return Inertia::render('Settings', ['pageTitle' => 'Settings']);
})->middleware(['auth', 'verified'])->name('settings');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
