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
    // Calcular duración en ms
    $duration = null;
    if (session()->has('auth_start_time')) {
        $duration = (microtime(true) - session('auth_start_time')) * 1000;
        session()->forget('auth_start_time');
    }

    // Tomar el método de sesión o fallback
    $authKind = session('auth_kind', 'unknown');
    session()->forget('auth_kind');

    DB::table('auth_metrics')->insert([
        'kind'        => $authKind, // ahora queda lo que guardaste en login
        'duration_ms' => $duration,
        'success'     => true,
        'user_id'     => $request->userId,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    return tap(to_route('dashboard'), fn () => $request->authenticate());
});


Route::post('logout', function (AuthKitLogoutRequest $request) {
    return $request->logout();
})->middleware(['auth'])->name('logout');