<?php

namespace Modules\Geography\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStreetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'town_id' => 'required|integer',
        ];
    }
}
