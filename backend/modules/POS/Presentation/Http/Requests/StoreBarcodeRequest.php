<?php

namespace Modules\POS\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarcodeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'barcode_number' => 'required|string|unique:pos_barcodes,barcode_number',
            'product_id'     => 'required|integer|exists:pos_products,id',
            'category_id'    => 'nullable|integer|exists:pos_categories,id',
        ];
    }
}
