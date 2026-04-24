<?php

namespace Modules\Email\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmailRecipientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'group_id' => 'nullable|integer',
        ];
    }
}
