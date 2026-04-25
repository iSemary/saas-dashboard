<?php

namespace Modules\HR\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScheduleInterviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:50'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:10'],
            'location' => ['nullable', 'string', 'max:255'],
            'meeting_link' => ['nullable', 'url'],
            'interviewer_ids' => ['nullable', 'array'],
            'interviewer_ids.*' => ['integer', 'exists:employees,id'],
        ];
    }
}
