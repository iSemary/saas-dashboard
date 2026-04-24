<?php

namespace Modules\Email\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailSubscriberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'sometimes|required|email|max:255',
            'name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }
}
