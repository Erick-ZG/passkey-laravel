<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'to_account_number' => ['required','string','exists:accounts,number'],
            'amount' => [
                'required',
                'numeric',
                'min:1',
                'max:999999999999.99',
                function ($attribute, $value, $fail) {
                    $account = $this->route('account'); // El account de la URL (ej: /accounts/{account}/transfers)

                    if ($account && $value > $account->balance) {
                        $fail('El monto excede el saldo disponible.');
                    }
                },
            ],
            'reference' => ['nullable','string','max:255'],
        ];
    }
}
