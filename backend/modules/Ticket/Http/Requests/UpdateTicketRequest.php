<?php

namespace Modules\Ticket\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => 'sometimes|required|string|max:255',
            'body' => 'nullable|string',
            'priority' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'category_id' => 'nullable|integer',
            'assigned_to' => 'nullable|integer',
        ];
    }
}
