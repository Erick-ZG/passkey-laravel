<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
{
    public function view(User $user, Account $account): bool
    {
        return $account->user_id === $user->id;
    }

    public function update(User $user, Account $account): bool
    {
        return $account->user_id === $user->id && $account->status === 'active';
    }

    public function operate(User $user, Account $account): bool
    {
        // Para depósitos/transferencias/pagos
        return $this->update($user, $account);
    }
}
