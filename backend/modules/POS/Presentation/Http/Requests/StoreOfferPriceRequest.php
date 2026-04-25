<?php

namespace Modules\POS\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOfferPriceRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'product_id'  => 'required|integer|exists:pos_products,id',
            'branch_id'   => 'nullable|integer',
            'amount'      => 'required|numeric|min:0',
            'buyer_name'  => 'nullable|string|max:255',
            'reduce_stock'=> 'boolean',
        ];
    }
}
