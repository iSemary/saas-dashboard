<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Entities\User;
use Modules\Survey\Domain\ValueObjects\SurveyCategory;

class SurveyTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'survey_templates';

    protected $fillable = [
        'name',
        'description',
        'category',
        'structure',
        'is_system',
        'created_by',
    ];

    protected $casts = [
        'structure' => 'array',
        'is_system' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getCategoryLabel(): string
    {
        return SurveyCategory::fromString($this->category)->label();
    }

    public function createSurveyFromTemplate(int $userId): Survey
    {
        return DB::transaction(function () use ($userId) {
            $structure = $this->structure;

            // Create survey
            $survey = Survey::create([
                'title' => $this->name,
                'description' => $this->description,
                'status' => 'draft',
                'template_id' => $this->id,
                'settings' => $structure['settings'] ?? [
                    'single_question_mode' => false,
                    'show_progress' => true,
                ],
                'created_by' => $userId,
            ]);

            // Create pages and questions from structure
            $pages = $structure['pages'] ?? [];
            foreach ($pages as $pageData) {
                $page = $survey->addPage([
                    'title' => $pageData['title'] ?? null,
                    'description' => $pageData['description'] ?? null,
                    'settings' => $pageData['settings'] ?? null,
                ]);

                $questions = $pageData['questions'] ?? [];
                foreach ($questions as $questionData) {
                    $question = $page->addQuestion([
                        'type' => $questionData['type'],
                        'title' => $questionData['title'],
                        'description' => $questionData['description'] ?? null,
                        'help_text' => $questionData['help_text'] ?? null,
                        'is_required' => $questionData['is_required'] ?? false,
                        'config' => $questionData['config'] ?? null,
                        'validation' => $questionData['validation'] ?? null,
                        'correct_answer' => $questionData['correct_answer'] ?? null,
                    ]);

                    // Add options if present
                    $options = $questionData['options'] ?? [];
                    foreach ($options as $optionData) {
                        $question->addOption([
                            'label' => $optionData['label'],
                            'value' => $optionData['value'] ?? $optionData['label'],
                            'image_url' => $optionData['image_url'] ?? null,
                            'is_other' => $optionData['is_other'] ?? false,
                            'point_value' => $optionData['point_value'] ?? 0,
                        ]);
                    }
                }
            }

            return $survey;
        });
    }
}
