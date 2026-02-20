<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:18', Rule::unique('suppliers', 'cnpj')->ignore($this->supplier)],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome e obrigatorio.',
            'cnpj.required' => 'O CNPJ e obrigatorio.',
            'cnpj.unique' => 'Este CNPJ ja esta cadastrado.',
            'email.required' => 'O e-mail e obrigatorio.',
            'email.email' => 'O e-mail deve ser valido.',
            'phone.required' => 'O telefone e obrigatorio.',
        ];
    }
}
