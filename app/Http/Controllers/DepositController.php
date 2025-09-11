<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Models\Account;
use App\Services\BankingService;

class DepositController extends Controller
{
    public function __construct(private BankingService $svc) {}

    public function store(DepositRequest $request, Account $account)
    {
        $this->middleware('account.owner');

        $this->authorize('operate', $account);

        $tx = $this->svc->deposit(
            $request->user(),
            $account,
            (float) $request->validated()['amount'],
            $request->validated()['reference'] ?? null
        );

        return redirect()->route('accounts.show', $account)
            ->with('success', 'Depósito realizado. Ref: '.$tx->id);
    }
}
