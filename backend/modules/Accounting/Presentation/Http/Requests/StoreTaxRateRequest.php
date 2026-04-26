<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaxRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'nullable|in:sales,purchase,withholding',
            'account_id' => 'nullable|integer|exists:acc_chart_of_accounts,id',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ];
    }
}
