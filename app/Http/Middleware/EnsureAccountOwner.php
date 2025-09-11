<?php

namespace App\Http\Middleware;

use App\Models\Account;
use Closure;
use Illuminate\Http\Request;

class EnsureAccountOwner
{
    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        /** @var Account|null $account */
        $account = $request->route('account');

        if ($account instanceof Account) {
            if ($account->user_id !== $user->id) {
                abort(403, 'No autorizado.');
            }
        }

        return $next($request);
    }
}
