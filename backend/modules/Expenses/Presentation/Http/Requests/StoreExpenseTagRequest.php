<?php

declare(strict_types=1);

namespace Modules\Expenses\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
        ];
    }
}
