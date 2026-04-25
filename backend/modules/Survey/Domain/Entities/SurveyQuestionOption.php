<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyQuestionOption extends Model
{
    protected $table = 'survey_question_options';

    protected $fillable = [
        'question_id',
        'label',
        'value',
        'order',
        'image_url',
        'is_other',
        'point_value',
    ];

    protected $casts = [
        'is_other' => 'boolean',
        'point_value' => 'integer',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }
}
