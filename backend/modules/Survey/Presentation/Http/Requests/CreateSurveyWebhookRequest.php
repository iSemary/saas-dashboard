<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSurveyWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'url' => 'required|url',
            'events' => 'required|array|min:1',
            'events.*' => 'string|in:survey.published,survey.closed,response.created,response.completed,response.partial,question.answered',
            'is_active' => 'boolean',
        ];
    }
}
