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
            'title'                     => 'required|string|max:150',
            'product_type'              => 'required|in:matcha,tool',
            'description'               => 'nullable|string',
            'image'                     => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery.*'                 => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants'                  => 'required|array|min:1',
            'variants.*.variant_name'      => 'required|string|max:255',
            'variants.*.price'          => 'required|numeric|min:0',
            'variants.*.stock'          => 'required|integer|min:0',
            'variants.*.discount_price' => 'nullable|numeric|lt:variants.*.price',
        ];
    }
}