<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin user management
Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('units', UnitController::class);
});

// Ticket Routes (Residents can submit, Admins can manage)
Route::middleware('auth')->group(function () {
    Route::get('/tickets/stats', [TicketController::class, 'getStats'])->name('tickets.stats');
    Route::resource('tickets', TicketController::class);
});

// Bill Routes (Billing Staff manages, Residents can view)
Route::middleware('auth')->group(function () {
    Route::get('/bills/stats', [BillController::class, 'getStats'])->name('bills.stats');
    Route::resource('bills', BillController::class);
});

// Payment Routes (Residents can pay, Billing Staff can view)
Route::middleware('auth')->group(function () {
    Route::get('/payments/stats', [PaymentController::class, 'getStats'])->name('payments.stats');
    Route::get('/payments/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/bill/{bill}', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/bill/{bill}', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
});

// HitPay Webhook (no auth required)
Route::post('/webhook/hitpay', [PaymentController::class, 'webhook'])->name('payments.webhook');

require __DIR__.'/auth.php';
