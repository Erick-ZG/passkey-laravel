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
        $start = session('auth_start_time');
        $duration = $start ? $start->diffInMilliseconds(now()) : null;

        DB::table('auth_metrics')->insert([
            'kind'        => session('auth_kind', 'unknown'),
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
