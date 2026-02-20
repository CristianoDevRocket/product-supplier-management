<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'internal_code' => ['required', 'string', 'max:50', 'unique:products,internal_code'],
            'status' => ['required', 'in:active,inactive'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome e obrigatorio.',
            'internal_code.required' => 'O codigo interno e obrigatorio.',
            'internal_code.unique' => 'Este codigo interno ja esta cadastrado.',
        ];
    }
}
