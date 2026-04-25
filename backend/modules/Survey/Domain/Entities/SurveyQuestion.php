<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Survey\Domain\ValueObjects\QuestionType;
use Modules\Survey\Domain\ValueObjects\BranchingOperator;

class SurveyQuestion extends Model
{
    protected $table = 'survey_questions';

    protected $fillable = [
        'survey_id',
        'page_id',
        'type',
        'title',
        'description',
        'help_text',
        'is_required',
        'order',
        'config',
        'validation',
        'branching',
        'correct_answer',
        'image_url',
    ];

    protected $casts = [
        'config' => 'array',
        'validation' => 'array',
        'branching' => 'array',
        'correct_answer' => 'array',
        'is_required' => 'boolean',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(SurveyPage::class, 'page_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(SurveyQuestionOption::class, 'question_id')->orderBy('order');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SurveyQuestionTranslation::class, 'question_id');
    }

    public function addOption(array $data): SurveyQuestionOption
    {
        $maxOrder = $this->options()->max('order') ?? 0;
        $data['order'] = $maxOrder + 1;
        $data['question_id'] = $this->id;

        return $this->options()->create($data);
    }

    public function reorderOptions(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            $this->options()->where('id', $id)->update(['order' => $index + 1]);
        }
    }

    public function hasBranching(): bool
    {
        return !empty($this->branching) && !empty($this->branching['conditions']);
    }

    public function evaluateBranching(SurveyResponse $response): array
    {
        if (!$this->hasBranching()) {
            return ['action' => 'show', 'target_id' => null];
        }

        $branching = $this->branching;
        $logic = $branching['logic'] ?? 'AND';
        $conditions = $branching['conditions'] ?? [];

        if (empty($conditions)) {
            return ['action' => 'show', 'target_id' => null];
        }

        $results = [];
        foreach ($conditions as $condition) {
            $results[] = $this->evaluateCondition($condition, $response);
        }

        $conditionMet = match(strtoupper($logic)) {
            'AND' => !in_array(false, $results, true),
            'OR' => in_array(true, $results, true),
            default => !in_array(false, $results, true),
        };

        if ($conditionMet) {
            return [
                'action' => $branching['action'] ?? 'show',
                'target_id' => $branching['target_id'] ?? null,
            ];
        }

        return ['action' => 'show', 'target_id' => null];
    }

    private function evaluateCondition(array $condition, SurveyResponse $response): bool
    {
        $targetQuestionId = $condition['question_id'] ?? null;
        if (!$targetQuestionId) {
            return false;
        }

        $answer = $response->answers()
            ->where('question_id', $targetQuestionId)
            ->first();

        if (!$answer) {
            $operator = BranchingOperator::fromString($condition['operator'] ?? 'eq');
            return $operator === BranchingOperator::IS_EMPTY;
        }

        $value = $answer->value;
        if ($answer->rating_value !== null) {
            $value = $answer->rating_value;
        }
        if ($answer->selected_options) {
            $options = is_string($answer->selected_options)
                ? json_decode($answer->selected_options, true)
                : $answer->selected_options;
            $value = array_column($options, 'option_id');
        }

        $operator = BranchingOperator::fromString($condition['operator'] ?? 'eq');
        $expected = $condition['value'] ?? null;

        return $operator->evaluate($value, $expected);
    }

    public function getQuestionType(): QuestionType
    {
        return QuestionType::fromString($this->type);
    }

    public function requiresOptions(): bool
    {
        return $this->getQuestionType()->requiresOptions();
    }

    public function supportsScoring(): bool
    {
        return $this->getQuestionType()->supportsScoring();
    }
}
