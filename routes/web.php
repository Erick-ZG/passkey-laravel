<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/webhooks/workos', function (\Illuminate\Http\Request $request) {
    $event = $request->input('event');
    $data  = $request->input('data');

    // Clasificar tipo
    $kind = match (true) {
        str_contains($event, 'passkey')   => 'passkey_setup',
        str_contains($event, 'password')  => 'password_creation',
        default                           => 'other',
    };

    // Determinar éxito o fallo
    $success = str_contains($event, 'succeeded');

    DB::table('auth_metrics')->insert([
        'kind'          => $kind,
        'success'       => $success,
        'error_code'    => $data['error']['code'] ?? null,
        'error_message' => $data['error']['message'] ?? null,
        'user_id'       => $data['user_id'] ?? null,
        'created_at'    => now(),
        'updated_at'    => now(),
    ]);

    return response()->json(['ok' => true]);
});

Route::middleware([
    'auth',
    ValidateSessionWithWorkOS::class,
])->group(function () {
    Route::get('dashboard', function () {
        /** @var User $user */
        $user = Auth::user();
        // cuentas del usuario
        $accounts = $user->accounts()->get(['id','number','name','currency','balance']);

        // total balance sumado
        $total = $accounts->sum('balance');

        // últimas 5 transacciones (de todas sus cuentas) - opcional
        $txs = \App\Models\Transaction::whereIn('account_id', $accounts->pluck('id')->toArray())
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id','account_id','type','amount','counterparty','created_at']);

        return Inertia::render('dashboard', [
            'accounts' => $accounts,
            'total_balance' => $total,
            'recent_transactions' => $txs,
        ]);
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
require __DIR__.'/banking.php';