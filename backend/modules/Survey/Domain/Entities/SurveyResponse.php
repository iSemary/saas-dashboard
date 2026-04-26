<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Modules\Auth\Entities\User;
use Modules\Survey\Domain\ValueObjects\ResponseStatus;
use Modules\Survey\Domain\Events\SurveyResponseCreated;
use Modules\Survey\Domain\Events\SurveyResponseCompleted;
use Modules\Customer\Entities\Tenant\Brand;

class SurveyResponse extends Model
{
    protected $table = 'survey_responses';

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeForBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    protected $fillable = [
        'survey_id',
        'share_id',
        'respondent_type',
        'respondent_id',
        'respondent_email',
        'respondent_name',
        'status',
        'started_at',
        'completed_at',
        'ip_address',
        'user_agent',
        'time_spent_seconds',
        'score',
        'max_score',
        'passed',
        'resume_token',
        'locale',
        'custom_fields',
        'brand_id',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'score' => 'integer',
        'max_score' => 'integer',
        'passed' => 'boolean',
        'custom_fields' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($response) {
            if (empty($response->started_at)) {
                $response->started_at = now();
            }
            if (empty($response->resume_token)) {
                $response->resume_token = Str::random(32);
            }
        });

        static::created(function ($response) {
            event(new SurveyResponseCreated($response));
        });
    }

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function share(): BelongsTo
    {
        return $this->belongsTo(SurveyShare::class, 'share_id');
    }

    public function respondent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondent_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'response_id');
    }

    public function addAnswer(array $data): SurveyAnswer
    {
        return $this->answers()->create($data);
    }

    public function complete(int $timeSpentSeconds = null): void
    {
        $this->update([
            'status' => ResponseStatus::COMPLETED->value,
            'completed_at' => now(),
            'time_spent_seconds' => $timeSpentSeconds ?? $this->calculateTimeSpent(),
        ]);

        event(new SurveyResponseCompleted($this));
    }

    public function disqualify(): void
    {
        $this->update([
            'status' => ResponseStatus::DISQUALIFIED->value,
            'completed_at' => now(),
        ]);
    }

    public function markPartial(): void
    {
        $this->update(['status' => ResponseStatus::PARTIAL->value]);
    }

    public function isCompleted(): bool
    {
        return $this->status === ResponseStatus::COMPLETED->value;
    }

    public function isPartial(): bool
    {
        return $this->status === ResponseStatus::PARTIAL->value;
    }

    public function isDisqualified(): bool
    {
        return $this->status === ResponseStatus::DISQUALIFIED->value;
    }

    public function canResume(): bool
    {
        return in_array($this->status, [
            ResponseStatus::STARTED->value,
            ResponseStatus::PARTIAL->value,
        ], true);
    }

    public function calculateTimeSpent(): int
    {
        return $this->started_at->diffInSeconds(now());
    }

    public function getAnswerForQuestion(int $questionId): ?SurveyAnswer
    {
        return $this->answers()->where('question_id', $questionId)->first();
    }

    public function hasAnswered(int $questionId): bool
    {
        return $this->answers()->where('question_id', $questionId)->exists();
    }

    public function getCompletionPercentage(): float
    {
        $totalQuestions = $this->survey->questions()->count();
        if ($totalQuestions === 0) {
            return 0.0;
        }

        $answered = $this->answers()->count();
        return round(($answered / $totalQuestions) * 100, 2);
    }

    public function updateScore(int $score, int $maxScore, ?bool $passed = null): void
    {
        $this->update([
            'score' => $score,
            'max_score' => $maxScore,
            'passed' => $passed,
        ]);
    }
}
