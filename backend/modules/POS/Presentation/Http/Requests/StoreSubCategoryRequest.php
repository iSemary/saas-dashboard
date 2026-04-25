<?php

namespace Modules\POS\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubCategoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'category_id' => 'required|integer|exists:pos_categories,id',
            'branch_id'   => 'nullable|integer',
        ];
    }
}
