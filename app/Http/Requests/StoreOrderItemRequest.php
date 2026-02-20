<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit_price' => ['required', 'numeric', 'min:0.01'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Selecione um produto.',
            'quantity.required' => 'A quantidade e obrigatoria.',
            'quantity.min' => 'A quantidade minima e 1.',
            'unit_price.required' => 'O preco unitario e obrigatorio.',
            'unit_price.min' => 'O preco minimo e R$ 0,01.',
        ];
    }
}
