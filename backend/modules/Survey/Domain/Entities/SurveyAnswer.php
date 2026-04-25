<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyAnswer extends Model
{
    protected $table = 'survey_answers';

    protected $fillable = [
        'response_id',
        'question_id',
        'value',
        'selected_options',
        'file_id',
        'matrix_answers',
        'rating_value',
        'computed_score',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'matrix_answers' => 'array',
        'rating_value' => 'integer',
        'computed_score' => 'integer',
        'file_id' => 'integer',
    ];

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class, 'response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    public function isValid(): bool
    {
        $question = $this->question;
        if (!$question) {
            return true;
        }

        if (!$question->is_required) {
            return true;
        }

        return $this->hasValue();
    }

    public function hasValue(): bool
    {
        if ($this->value !== null && $this->value !== '') {
            return true;
        }

        if ($this->rating_value !== null) {
            return true;
        }

        if (!empty($this->selected_options)) {
            return true;
        }

        if (!empty($this->matrix_answers)) {
            return true;
        }

        if ($this->file_id !== null) {
            return true;
        }

        return false;
    }

    public function getFormattedValue(): string
    {
        if ($this->value !== null) {
            return $this->value;
        }

        if ($this->rating_value !== null) {
            return (string) $this->rating_value;
        }

        if (!empty($this->selected_options)) {
            $options = is_string($this->selected_options)
                ? json_decode($this->selected_options, true)
                : $this->selected_options;

            $labels = [];
            foreach ($options as $opt) {
                if (!empty($opt['other_text'])) {
                    $labels[] = $opt['other_text'];
                } else {
                    $optionId = $opt['option_id'] ?? null;
                    if ($optionId) {
                        $dbOption = $this->question?->options()->find($optionId);
                        $labels[] = $dbOption?->label ?? $optionId;
                    }
                }
            }
            return implode(', ', $labels);
        }

        if (!empty($this->matrix_answers)) {
            $matrix = is_string($this->matrix_answers)
                ? json_decode($this->matrix_answers, true)
                : $this->matrix_answers;
            return json_encode($matrix);
        }

        return '';
    }
}
