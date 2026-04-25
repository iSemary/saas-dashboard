<?php

namespace Modules\HR\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApplyToJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_opening_id' => ['required', 'integer', 'exists:job_openings,id'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'cover_letter' => ['nullable', 'string'],
            'source' => ['nullable', 'string', 'max:100'],
            'salary_expectation' => ['nullable', 'numeric', 'min:0'],
            'available_from' => ['nullable', 'date'],
        ];
    }
}
