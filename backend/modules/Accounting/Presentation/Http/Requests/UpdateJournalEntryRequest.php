<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entry_number' => 'nullable|string|max:50',
            'entry_date' => 'required|date',
            'state' => 'nullable|in:draft,posted,cancelled',
            'reference' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'currency' => 'nullable|string|size:3',
            'fiscal_year_id' => 'required|integer|exists:acc_fiscal_years,id',
            'items' => 'nullable|array',
            'items.*.account_id' => 'required|integer|exists:acc_chart_of_accounts,id',
            'items.*.debit' => 'nullable|numeric|min:0',
            'items.*.credit' => 'nullable|numeric|min:0',
        ];
    }
}
