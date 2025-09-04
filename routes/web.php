<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Illuminate\Support\Facades\DB;

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
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
