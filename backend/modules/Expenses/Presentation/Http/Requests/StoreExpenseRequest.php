<?php

declare(strict_types=1);

namespace Modules\Expenses\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'date' => 'required|date',
            'category_id' => 'required|integer|exists:exp_categories,id',
            'reference' => 'nullable|string|max:100',
            'vendor' => 'nullable|string|max:255',
            'receipt' => 'nullable|string',
            'receipt_date' => 'nullable|date',
            'is_billable' => 'nullable|boolean',
            'project_id' => 'nullable|integer',
            'department_id' => 'nullable|integer',
            'report_id' => 'nullable|integer|exists:exp_reports,id',
        ];
    }
}
