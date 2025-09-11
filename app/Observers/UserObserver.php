<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Account;

class UserObserver
{
    public function created(User $user)
    {
        // Cuenta principal
        Account::create([
            'user_id' => $user->id,
            'number' => 'USD-'.str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT),
            'name' => 'Cuenta principal',
            'currency' => 'USD',
            'balance' => 5000,
            'is_primary' => true,
        ]);

        // Cuenta de ahorros
        Account::create([
            'user_id' => $user->id,
            'number' => 'USD-'.str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT),
            'name' => 'Ahorros',
            'currency' => 'USD',
            'balance' => 2000,
        ]);
    }
}
