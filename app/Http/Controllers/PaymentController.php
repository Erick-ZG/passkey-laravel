<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Account;
use App\Models\Merchant;
use App\Services\BankingService;

class PaymentController extends Controller
{
    public function __construct(private BankingService $svc) {}

    public function create(Account $account)
    {
        $this->authorize('view', $account);

        return inertia('payments/create', [
            'account' => $account->only('id','number','name','currency','balance'),
            'merchants' => Merchant::where('status','active')->orderBy('name')->get(['id','name','category']),
        ]);
    }

    public function store(PaymentRequest $request, Account $account)
    {
        $this->authorize('operate', $account);

        $merchant = Merchant::findOrFail($request->validated()['merchant_id']);

        $payment = $this->svc->pay(
            $request->user(), $account, $merchant,
            (float) $request->validated()['amount'],
            $request->validated()['description'] ?? null
        );

        return redirect()->route('accounts.show', $account)
            ->with('success', 'Pago realizado. Ref: '.$payment->transaction_id);
    }
}
