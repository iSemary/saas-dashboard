<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmContactListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,archived',
        ];
    }
}
