<?php

declare(strict_types=1);

namespace Modules\Expenses\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:exp_categories,id',
            'is_active' => 'nullable|boolean',
            'default_account_id' => 'nullable|integer',
            'requires_receipt' => 'nullable|boolean',
            'max_amount' => 'nullable|numeric|min:0',
        ];
    }
}
