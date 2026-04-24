<?php

namespace Modules\Subscription\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'currency' => 'nullable|string|max:10',
            'billing_period' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ];
    }
}
