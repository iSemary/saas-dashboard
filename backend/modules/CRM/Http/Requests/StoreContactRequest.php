<?php

namespace Modules\CRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
            'company_id' => 'nullable|exists:companies,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'notes' => 'nullable|string',
            'type' => 'nullable|in:individual,company',
            'assigned_to' => 'nullable|exists:users,id',
            'custom_fields' => 'nullable|array',
        ];
    }
}
