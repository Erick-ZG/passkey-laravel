<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Models\Account;
use App\Services\BankingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

class TransferController extends Controller
{
    public function __construct(private BankingService $svc) {}

    public function store(TransferRequest $request, Account $account)
    {
        $this->middleware('account.owner');

        $this->authorize('operate', $account);

        $data = $request->validated();

        $to = Account::where('number', $data['to_account_number'])
            ->where('status','active')
            ->first();

        if (!$to) {
            return redirect()->back()->with('error', 'Cuenta destino no existe o no está activa.');
        }

        try {
            [$out, $in] = $this->svc->transfer(
                $request->user(), $account, $to,
                (float) $data['amount'],
                $data['reference'] ?? null
            );

            return redirect()->route('accounts.show', $account)
                ->with('success', 'Transferencia realizada. Ref: '.$out->id);

        } catch (InvalidArgumentException $e) {
            // Fondos insuficientes u otra validación de negocio
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            // Cualquier otro error inesperado
            return redirect()->back()->with('error', 'Ocurrió un error inesperado.');
        }
    }
}
