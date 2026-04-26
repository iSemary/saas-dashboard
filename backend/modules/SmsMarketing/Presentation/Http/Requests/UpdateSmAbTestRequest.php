<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSmAbTestRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'variant_name' => 'sometimes|string|max:255',
            'body' => 'nullable|string',
            'percentage' => 'nullable|integer|min:1|max:99',
            'winner' => 'nullable|string|max:255',
        ];
    }
}
