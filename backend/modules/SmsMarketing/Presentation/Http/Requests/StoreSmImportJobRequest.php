<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSmImportJobRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'contact_list_id' => 'required|integer',
            'file_path' => 'required|string|max:500',
            'column_mapping' => 'nullable|array',
        ];
    }
}
