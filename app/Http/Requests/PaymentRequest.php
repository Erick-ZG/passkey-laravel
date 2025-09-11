<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'merchant_id' => ['required','exists:merchants,id'],
            'amount' => ['required','numeric','min:1','max:999999999999.99'],
            'description' => ['nullable','string','max:255'],
        ];
    }
}
