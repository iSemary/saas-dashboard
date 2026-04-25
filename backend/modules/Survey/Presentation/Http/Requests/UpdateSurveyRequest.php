<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurveyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'theme_id' => 'nullable|integer|exists:survey_themes,id',
            'settings' => 'nullable|array',
            'default_locale' => 'nullable|string|max:10',
            'supported_locales' => 'nullable|array',
        ];
    }
}
