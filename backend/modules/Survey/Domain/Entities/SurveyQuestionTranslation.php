<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuestionTranslation extends Model
{
    protected $table = 'survey_question_translations';

    protected $fillable = [
        'question_id',
        'locale',
        'title',
        'description',
        'help_text',
        'options_translations',
    ];

    protected $casts = [
        'options_translations' => 'array',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }
}
