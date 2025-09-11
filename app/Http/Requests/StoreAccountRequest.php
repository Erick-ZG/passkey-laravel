<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El middleware 'auth' ya protege las rutas; aquí devolvemos true.
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:80'],
            'currency' => ['required','string','size:3'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la cuenta es obligatorio.',
            'currency.required' => 'Selecciona la moneda.',
        ];
    }
}
