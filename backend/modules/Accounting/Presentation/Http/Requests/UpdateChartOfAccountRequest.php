<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChartOfAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'sometimes|string|max:50|unique:acc_chart_of_accounts,code,' . request()->route('chart_of_account'),
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'sub_type' => 'nullable|string',
            'parent_id' => 'nullable|integer|exists:acc_chart_of_accounts,id',
            'level' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'is_leaf' => 'nullable|boolean',
            'reconcile' => 'nullable|boolean',
            'currency' => 'nullable|string|size:3',
            'opening_balance' => 'nullable|numeric',
        ];
    }
}
