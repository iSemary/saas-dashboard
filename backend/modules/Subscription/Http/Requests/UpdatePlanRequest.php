<?php

namespace Modules\Subscription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'currency' => 'nullable|string|max:10',
            'billing_period' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ];
    }
}
