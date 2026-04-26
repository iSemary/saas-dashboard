<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'fiscal_year_id' => 'required|integer|exists:acc_fiscal_years,id',
            'department_id' => 'nullable|integer',
            'status' => 'nullable|in:draft,active,archived',
            'total_amount' => 'nullable|numeric',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'items' => 'nullable|array',
            'items.*.account_id' => 'required|integer|exists:acc_chart_of_accounts,id',
            'items.*.amount' => 'required|numeric|min:0',
        ];
    }
}
