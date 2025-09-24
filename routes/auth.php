<?php

use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLoginRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLogoutRequest;
use Illuminate\Support\Facades\DB;

Route::get('login', function (AuthKitLoginRequest $request) {
    // Guardar tiempo inicial como float
    session(['auth_start_time' => microtime(true)]);
    
    // Guardar el método (puedes pasar ?method=passkey_creation o ?method=password_creation)
    session(['auth_kind' => request('method', 'password_creation')]); 

    return $request->redirect();
})->middleware(['guest'])->name('login');


Route::get('/authenticate', function (AuthKitAuthenticationRequest $request) {
    $duration = null;
    if (session()->has('auth_start_time')) {
        $duration = (microtime(true) - session('auth_start_time')) * 1000;
        session()->forget('auth_start_time');
    }

    $authPayload = $request->toArray();
    $authMethod  = $authPayload['authentication']['method'] ?? 'unknown';

    // **Primero** crea la sesión del usuario
    $request->authenticate();

    DB::table('auth_metrics')->insert([
        'kind'        => $authMethod,
        'duration_ms' => $duration,
        'success'     => true,
        'user_id'     => $request->userId,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    return redirect()->route('dashboard');
});


Route::post('logout', function (AuthKitLogoutRequest $request) {
    return $request->logout();
})->middleware(['auth'])->name('logout');