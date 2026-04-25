<?php

namespace Modules\HR\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdvanceApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pipeline_stage_id' => ['required', 'integer', 'exists:pipeline_stages,id'],
        ];
    }
}
