<?php

namespace Modules\POS\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $productId = $this->route('product') ?? $this->route('id');

        return [
            'name'             => 'sometimes|string|max:255',
            'amount'           => 'sometimes|numeric|min:0',
            'amount_type'      => 'nullable|integer|exists:pos_types,id',
            'description'      => 'nullable|string',
            'image'            => 'nullable|string',
            'purchase_price'   => 'sometimes|numeric|min:0',
            'sale_price'       => 'sometimes|numeric|min:0',
            'supplier_id'      => 'nullable|integer',
            'category_id'      => 'nullable|integer|exists:pos_categories,id',
            'sub_category_id'  => 'nullable|integer|exists:pos_sub_categories,id',
            'is_offer'         => 'boolean',
            'offer_percentage' => 'nullable|numeric|min:0|max:100',
            'production_at'    => 'nullable|date',
            'expired_at'       => 'nullable|date',
            'barcode_number'   => "nullable|string|unique:pos_barcodes,barcode_number,NULL,id,product_id,{$productId}",
            'tag_id'           => 'nullable|integer|exists:pos_tags,id',
        ];
    }
}
