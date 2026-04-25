<?php

declare(strict_types=1);

namespace Modules\Survey\Presentation\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSurveyQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'survey_id' => 'required|integer|exists:surveys,id',
            'page_id' => 'required|integer|exists:survey_pages,id',
            'type' => 'required|string|in:text,textarea,email,number,date,rating,nps,multiple_choice,checkbox,radio,matrix,likert_scale,yes_no,file_upload',
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'help_text' => 'nullable|string',
            'is_required' => 'boolean',
            'config' => 'nullable|array',
            'validation' => 'nullable|array',
            'branching' => 'nullable|array',
            'correct_answer' => 'nullable',
            'image_url' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.label' => 'required_with:options|string',
            'options.*.value' => 'nullable|string',
        ];
    }
}
