<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransferController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

Route::middleware(['auth', ValidateSessionWithWorkOS::class])->group(function () {
    // Accounts
    Route::get('accounts/create', [AccountController::class, 'create'])
    ->name('accounts.create');
    
    Route::resource('accounts', AccountController::class)->only(['index','show','store','destroy']);

    // Forms simples con Inertia (si quieres páginas separadas)
    Route::get('accounts/{account}/deposit', fn(\App\Models\Account $account) =>
        Inertia::render('deposits/create', ['account' => $account->only('id','number','name','currency','balance')])
    )->name('deposits.create')->middleware('account.owner');

    Route::get('accounts/{account}/transfer', fn(\App\Models\Account $account) =>
        Inertia::render('transfers/create', ['account' => $account->only('id','number','name','currency','balance')])
    )->name('transfers.create')->middleware('account.owner');

    // Actions
    Route::post('accounts/{account}/deposit', [DepositController::class, 'store'])
        ->name('deposits.store')->middleware('account.owner');

    Route::post('accounts/{account}/transfer', [TransferController::class, 'store'])
        ->name('transfers.store')->middleware('account.owner');

    Route::get('accounts/{account}/payments/create', [PaymentController::class, 'create'])
        ->name('payments.create')->middleware('account.owner');

    Route::post('accounts/{account}/payments', [PaymentController::class, 'store'])
        ->name('payments.store')->middleware('account.owner');
});
