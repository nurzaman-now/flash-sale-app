<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
     * Aturan validasi yang berlaku untuk request ini.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ];
    }

    /**
     * Mengembalikan pesan error.
     */
    public function messages()
    {
        return [
            'name.required' => ':attribute wajib diisi.',
            'name.string' => ':attribute harus berupa string.',
            'name.max' => ':attribute maksimal :max karakter.',
            'stock.required' => ':attribute wajib diisi.',
            'stock.integer' => ':attribute harus berupa angka bulat.',
            'stock.min' => ':attribute minimal :min.',
            'price.required' => ':attribute wajib diisi.',
            'price.numeric' => ':attribute harus berupa angka.',
            'price.min' => ':attribute minimal :min.',
        ];
    }

    /**
     * Mengembalikan nama atribut.
     */
    public function attributes()
    {
        return [
            'name' => 'Nama Produk',
            'stock' => 'Stock',
            'price' => 'Harga',
        ];
    }
}
