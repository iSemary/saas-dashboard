<?php

declare(strict_types=1);

namespace Modules\Accounting\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'branch_code' => 'nullable|string|max:50',
            'swift_code' => 'nullable|string|max:20',
            'iban' => 'nullable|string|max:34',
            'currency' => 'nullable|string|size:3',
            'opening_balance' => 'nullable|numeric',
            'account_id' => 'nullable|integer|exists:acc_chart_of_accounts,id',
            'is_active' => 'nullable|boolean',
            'description' => 'nullable|string',
        ];
    }
}
