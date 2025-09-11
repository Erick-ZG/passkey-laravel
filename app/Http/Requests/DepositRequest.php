<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount' => ['required','numeric','min:1','max:999999999999.99'],
            'reference' => ['nullable','string','max:255'],
        ];
    }
}
