<?php

namespace Modules\POS\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'                    => 'required|string|max:255',
            'amount'                  => 'required|numeric|min:0',
            'amount_type'             => 'nullable|integer|exists:pos_types,id',
            'description'             => 'nullable|string',
            'image'                   => 'nullable|string',
            'purchase_price'          => 'required|numeric|min:0',
            'sale_price'              => 'required|numeric|min:0',
            'supplier_id'             => 'nullable|integer',
            'category_id'             => 'nullable|integer|exists:pos_categories,id',
            'sub_category_id'         => 'nullable|integer|exists:pos_sub_categories,id',
            'is_offer'                => 'boolean',
            'offer_percentage'        => 'nullable|numeric|min:0|max:100',
            'type'                    => 'integer|in:1,2',
            'production_at'           => 'nullable|date',
            'expired_at'              => 'nullable|date',
            'barcode_number'          => 'nullable|string|unique:pos_barcodes,barcode_number',
            'tag_id'                  => 'nullable|integer|exists:pos_tags,id',
            'wholesale_purchase_price'=> 'nullable|numeric|min:0',
            'wholesale_sale_price'    => 'nullable|numeric|min:0',
            'wholesale_quantity'      => 'nullable|integer|min:1',
            'wholesale_barcode'       => 'nullable|string',
            'wholesale_product'       => 'boolean',
        ];
    }
}
