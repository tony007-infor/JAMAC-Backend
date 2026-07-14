<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => [
                'required',
                'uuid',
                Rule::exists('customers', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required',
                'uuid',
                Rule::exists('products', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'El cliente es obligatorio.',
            'customer_id.exists' => 'El cliente seleccionado no es válido o ha sido eliminado.',
            'items.required' => 'El carrito de compras debe tener al menos un producto.',
            'items.min' => 'El carrito de compras debe tener al menos un producto.',
            'items.*.product_id.required' => 'El ID del producto es obligatorio.',
            'items.*.product_id.exists' => 'Uno de los productos seleccionados no es válido o ha sido eliminado.',
            'items.*.quantity.required' => 'La cantidad del producto es obligatoria.',
            'items.*.quantity.min' => 'La cantidad de cada producto debe ser al menos 1.',
        ];
    }
}
