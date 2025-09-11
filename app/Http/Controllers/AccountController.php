<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Requests\StoreAccountRequest;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        $accounts = Account::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('is_primary')
            ->get(['id','number','name','currency','balance','status','created_at']);

        return Inertia::render('accounts/index', [
            'accounts' => $accounts,
        ]);
    }

    public function show(Account $account)
    {
        $this->authorize('view', $account);

        $transactions = $account->transactions()
            ->select('id','type','amount','balance_after','reference','counterparty','status','created_at')
            ->paginate(15);

        return Inertia::render('accounts/show', [
            'account' => $account->only('id','number','name','currency','balance','status'),
            'transactions' => $transactions,
        ]);
    }

    // Mostrar formulario de creación (Inertia)
    public function create()
    {
        // Opciones de monedas que quieras ofrecer (puedes ampliarlas)
        $currencies = [
            ['code' => 'USD', 'label' => 'USD - Dólar estadounidense'],
            ['code' => 'EUR', 'label' => 'EUR - Euro'],
            ['code' => 'PEN', 'label' => 'PEN - Sol peruano'],
        ];

        return Inertia::render('accounts/create', [
            'currencies' => $currencies,
        ]);
    }

    // Guardar cuenta (usa el Request validado)
    public function store(StoreAccountRequest $request)
    {
        $data = $request->validated();

        $number = strtoupper($data['currency']) . '-' . str_pad((string) random_int(1, 99999999), 8, '0', STR_PAD_LEFT);

        $account = Account::create([
            'user_id' => $request->user()->id,
            'number'  => $number,
            'name'    => $data['name'],
            'currency'=> strtoupper($data['currency']),
            'is_primary' => false,
        ]);

        return to_route('accounts.show', $account)->with('success', 'Cuenta creada correctamente.');
    }

    public function destroy(Account $account)
    {
        $this->authorize('update', $account);
        $account->status = 'closed';
        $account->save();
        $account->delete();

        return back()->with('success','Cuenta cerrada');
    }
}
