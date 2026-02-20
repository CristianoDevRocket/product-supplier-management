<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['required', 'integer', 'exists:products,id'],
            'supplier_ids' => ['required', 'array', 'min:1'],
            'supplier_ids.*' => ['required', 'integer', 'exists:suppliers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_ids.required' => 'Selecione ao menos um produto.',
            'supplier_ids.required' => 'Selecione ao menos um fornecedor.',
        ];
    }
}
