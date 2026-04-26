<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmContactListRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:active,archived',
        ];
    }
}
