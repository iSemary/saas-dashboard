<?php

declare(strict_types=1);

namespace Modules\Expenses\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:100',
            'color' => 'nullable|string|max:7',
        ];
    }
}
