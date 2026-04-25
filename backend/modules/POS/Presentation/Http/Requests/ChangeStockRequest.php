<?php

namespace Modules\POS\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStockRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'amount'    => 'required|numeric|min:0.01',
            'direction' => 'required|in:increment,decrement',
            'branch_id' => 'nullable|integer',
        ];
    }
}
