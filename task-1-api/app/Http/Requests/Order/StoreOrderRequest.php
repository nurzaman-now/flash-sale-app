<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Menentukan apakah user berhak membuat request ini.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Aturan validasi yang diterapkan.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    /**
     * Mengembalikan pesan error.
     */
    public function messages()
    {
        return [
            'items.required' => 'Items wajib diisi.',
            'items.array' => 'Items harus dalam bentuk array.',
            'items.min' => 'Items minimal harus ada 1.',
            'items.*.product_id.required' => 'Product ID wajib diisi.',
            'items.*.product_id.exists' => 'Product tidak ditemukan.',
            'items.*.quantity.required' => 'Quantity wajib diisi.',
            'items.*.quantity.integer' => 'Quantity harus dalam bentuk integer.',
            'items.*.quantity.min' => 'Quantity minimal harus 1.',
        ];
    }

    /**
     * Mengembalikan nama atribut.
     */
    public function attributes()
    {
        return [
            'items' => 'Items',
            'items.*.product_id' => 'Product ID',
            'items.*.quantity' => 'Quantity',
        ];
    }
}
