<?php

use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Requests\AuthKitAuthenticationRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLoginRequest;
use Laravel\WorkOS\Http\Requests\AuthKitLogoutRequest;
use Illuminate\Support\Facades\DB;

Route::get('login', function (AuthKitLoginRequest $request) {
    session(['auth_start_time' => now()]);
    session(['auth_kind' => request('method', 'password_creation')]); 
    // puedes pasar ?method=passkey_setup o ?method=password_creation
    return $request->redirect();
})->middleware(['guest'])->name('login');


Route::get('/authenticate', function (AuthKitAuthenticationRequest $request) {
    $authMethod = $request->json('authentication.method') ?? 'unknown';

    DB::table('auth_metrics')->insert([
        'kind'        => $authMethod, // aquí ya vendría 'passkey' o 'password'
        'duration_ms' => session('auth_start_time') 
                          ? (microtime(true) - session('auth_start_time')) * 1000 
                          : null,
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
