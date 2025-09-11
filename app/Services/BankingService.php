<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Merchant;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BankingService
{
    /**
     * Depósito en cuenta del usuario.
     */
    public function deposit(User $user, Account $account, float $amount, ?string $reference = null): Transaction
    {
        return DB::transaction(function () use ($user, $account, $amount, $reference) {
            // lock
            $acc = Account::whereKey($account->id)->lockForUpdate()->first();

            if ($acc->status !== 'active') {
                throw new InvalidArgumentException('Cuenta no activa.');
            }

            $acc->balance = bcadd((string)$acc->balance, (string)$amount, 2);
            $acc->save();

            return Transaction::create([
                'account_id'            => $acc->id,
                'performed_by_user_id'  => $user->id,
                'type'                  => 'deposit',
                'amount'                => $amount,
                'balance_after'         => $acc->balance,
                'reference'             => $reference,
                'status'                => 'completed',
            ]);
        });
    }

    /**
     * Transferencia entre dos cuentas internas.
     */
    public function transfer(User $user, Account $from, Account $to, float $amount, ?string $reference = null): array
    {
        if ($from->id === $to->id) {
            throw new InvalidArgumentException('No puedes transferir a la misma cuenta.');
        }

        return DB::transaction(function () use ($user, $from, $to, $amount, $reference) {
            $src = Account::whereKey($from->id)->lockForUpdate()->first();
            $dst = Account::whereKey($to->id)->lockForUpdate()->first();

            if ($src->status !== 'active' || $dst->status !== 'active') {
                throw new InvalidArgumentException('Cuenta origen o destino no activa.');
            }
            if (bccomp((string)$src->balance, (string)$amount, 2) < 0) {
                throw new InvalidArgumentException('Fondos insuficientes.');
            }

            // debitar origen
            $src->balance = bcsub((string)$src->balance, (string)$amount, 2);
            $src->save();

            $out = Transaction::create([
                'account_id'           => $src->id,
                'performed_by_user_id' => $user->id,
                'type'                 => 'transfer_out',
                'amount'               => $amount,
                'balance_after'        => $src->balance,
                'reference'            => $reference,
                'counterparty'         => $to->number,
            ]);

            // acreditar destino
            $dst->balance = bcadd((string)$dst->balance, (string)$amount, 2);
            $dst->save();

            $in = Transaction::create([
                'account_id'           => $dst->id,
                'performed_by_user_id' => $user->id,
                'type'                 => 'transfer_in',
                'amount'               => $amount,
                'balance_after'        => $dst->balance,
                'reference'            => $reference,
                'counterparty'         => $from->number,
                'related_transaction_id' => $out->id,
            ]);

            $out->related_transaction_id = $in->id;
            $out->save();

            return [$out, $in];
        });
    }

    /**
     * Pago a comercio (crea transaction + payment).
     */
    public function pay(User $user, Account $from, Merchant $merchant, float $amount, ?string $description = null): Payment
    {
        return DB::transaction(function () use ($user, $from, $merchant, $amount, $description) {
            $acc = Account::whereKey($from->id)->lockForUpdate()->first();

            if ($acc->status !== 'active') {
                throw new InvalidArgumentException('Cuenta no activa.');
            }
            if (bccomp((string)$acc->balance, (string)$amount, 2) < 0) {
                throw new InvalidArgumentException('Fondos insuficientes.');
            }

            $acc->balance = bcsub((string)$acc->balance, (string)$amount, 2);
            $acc->save();

            $tx = Transaction::create([
                'account_id'           => $acc->id,
                'performed_by_user_id' => $user->id,
                'type'                 => 'payment',
                'amount'               => $amount,
                'balance_after'        => $acc->balance,
                'reference'            => $description,
                'counterparty'         => $merchant->name,
                'metadata'             => ['merchant_id' => $merchant->id],
            ]);

            return Payment::create([
                'user_id'       => $user->id,
                'account_id'    => $acc->id,
                'merchant_id'   => $merchant->id,
                'amount'        => $amount,
                'description'   => $description,
                'transaction_id'=> $tx->id,
                'status'        => 'completed',
            ]);
        });
    }
}
