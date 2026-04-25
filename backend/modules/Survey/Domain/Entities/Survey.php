<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Entities\User;
use Modules\Survey\Domain\ValueObjects\SurveyStatus;
use Modules\Survey\Domain\Exceptions\InvalidSurveyStatusTransition;
use Modules\Survey\Domain\Exceptions\SurveyAlreadyPublishedException;
use Modules\Survey\Domain\Exceptions\SurveyNotPublishableException;
use Modules\Survey\Domain\Events\SurveyCreated;
use Modules\Survey\Domain\Events\SurveyPublished;
use Modules\Survey\Domain\Events\SurveyClosed;

class Survey extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surveys';

    protected $fillable = [
        'title',
        'description',
        'status',
        'settings',
        'theme_id',
        'template_id',
        'default_locale',
        'supported_locales',
        'published_at',
        'closed_at',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
        'supported_locales' => 'array',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($survey) {
            if (empty($survey->status)) {
                $survey->status = SurveyStatus::DRAFT->value;
            }
            if (empty($survey->default_locale)) {
                $survey->default_locale = 'en';
            }
        });

        static::created(function ($survey) {
            event(new SurveyCreated($survey));
        });
    }

    // ── Relationships ─────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function theme(): BelongsTo
    {
        return $this->belongsTo(SurveyTheme::class, 'theme_id');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(SurveyTemplate::class, 'template_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(SurveyPage::class, 'survey_id')->orderBy('order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'survey_id')->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class, 'survey_id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(SurveyShare::class, 'survey_id');
    }

    public function automationRules(): HasMany
    {
        return $this->hasMany(SurveyAutomationRule::class, 'survey_id');
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(SurveyWebhook::class, 'survey_id');
    }

    // ── Scopes ────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', SurveyStatus::ACTIVE->value);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', SurveyStatus::DRAFT->value);
    }

    public function scopePublished($query)
    {
        return $query->whereIn('status', [
            SurveyStatus::ACTIVE->value,
            SurveyStatus::PAUSED->value,
            SurveyStatus::CLOSED->value,
        ]);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Business Methods ──────────────────────────────────

    public function transitionStatus(SurveyStatus $newStatus): void
    {
        $currentStatus = SurveyStatus::fromString($this->status);

        if (!SurveyStatus::canTransitionFrom($currentStatus, $newStatus)) {
            throw new InvalidSurveyStatusTransition($this->status, $newStatus->value);
        }

        $this->update(['status' => $newStatus->value]);
    }

    public function canPublish(): bool
    {
        $errors = [];

        if (empty($this->title)) {
            $errors[] = 'Survey title is required';
        }

        if ($this->questions()->count() === 0) {
            $errors[] = 'Survey must have at least one question';
        }

        // Check for questions without titles
        $invalidQuestions = $this->questions()
            ->whereNull('title')
            ->orWhere('title', '')
            ->count();

        if ($invalidQuestions > 0) {
            $errors[] = "{$invalidQuestions} question(s) missing titles";
        }

        return empty($errors);
    }

    public function publish(): void
    {
        if ($this->status === SurveyStatus::ACTIVE->value) {
            throw new SurveyAlreadyPublishedException($this->id);
        }

        if (!$this->canPublish()) {
            $errors = [];
            if (empty($this->title)) {
                $errors[] = 'Survey title is required';
            }
            if ($this->questions()->count() === 0) {
                $errors[] = 'Survey must have at least one question';
            }
            throw new SurveyNotPublishableException($this->id, $errors);
        }

        $this->update([
            'status' => SurveyStatus::ACTIVE->value,
            'published_at' => now(),
        ]);

        event(new SurveyPublished($this));
    }

    public function pause(): void
    {
        $this->transitionStatus(SurveyStatus::PAUSED);
    }

    public function resume(): void
    {
        $this->transitionStatus(SurveyStatus::ACTIVE);
    }

    public function close(): void
    {
        $this->update([
            'status' => SurveyStatus::CLOSED->value,
            'closed_at' => now(),
        ]);

        event(new SurveyClosed($this));
    }

    public function archive(): void
    {
        $this->transitionStatus(SurveyStatus::ARCHIVED);
    }

    public function duplicate(): self
    {
        return DB::transaction(function () {
            // Create new survey with same data
            $newSurvey = self::create([
                'title' => $this->title . ' (Copy)',
                'description' => $this->description,
                'status' => SurveyStatus::DRAFT->value,
                'settings' => $this->settings,
                'theme_id' => $this->theme_id,
                'default_locale' => $this->default_locale,
                'supported_locales' => $this->supported_locales,
                'created_by' => auth()->id(),
            ]);

            // Duplicate pages
            foreach ($this->pages as $page) {
                $newPage = $newSurvey->pages()->create([
                    'title' => $page->title,
                    'description' => $page->description,
                    'order' => $page->order,
                    'settings' => $page->settings,
                ]);

                // Duplicate questions for this page
                foreach ($page->questions as $question) {
                    $newQuestion = $newPage->questions()->create([
                        'survey_id' => $newSurvey->id,
                        'type' => $question->type,
                        'title' => $question->title,
                        'description' => $question->description,
                        'help_text' => $question->help_text,
                        'is_required' => $question->is_required,
                        'order' => $question->order,
                        'config' => $question->config,
                        'validation' => $question->validation,
                        'branching' => $question->branching,
                        'correct_answer' => $question->correct_answer,
                        'image_url' => $question->image_url,
                    ]);

                    // Duplicate options
                    foreach ($question->options as $option) {
                        $newQuestion->options()->create([
                            'label' => $option->label,
                            'value' => $option->value,
                            'order' => $option->order,
                            'image_url' => $option->image_url,
                            'is_other' => $option->is_other,
                            'point_value' => $option->point_value,
                        ]);
                    }
                }
            }

            return $newSurvey;
        });
    }

    public function addPage(array $data): SurveyPage
    {
        $maxOrder = $this->pages()->max('order') ?? 0;
        $data['order'] = $maxOrder + 1;
        $data['survey_id'] = $this->id;

        return $this->pages()->create($data);
    }

    public function getCompletionRate(): float
    {
        $total = $this->responses()->count();
        if ($total === 0) {
            return 0.0;
        }

        $completed = $this->responses()
            ->where('status', 'completed')
            ->count();

        return round(($completed / $total) * 100, 2);
    }

    public function getTotalResponses(): int
    {
        return $this->responses()->count();
    }

    public function getCompletedResponses(): int
    {
        return $this->responses()->where('status', 'completed')->count();
    }

    public function isActive(): bool
    {
        return $this->status === SurveyStatus::ACTIVE->value;
    }

    public function isDraft(): bool
    {
        return $this->status === SurveyStatus::DRAFT->value;
    }

    public function isClosed(): bool
    {
        return $this->status === SurveyStatus::CLOSED->value;
    }

    public function isQuiz(): bool
    {
        return $this->settings['is_scored'] ?? false;
    }

    // ── Value Object Accessors ───────────────────────────

    public function getStatusAttribute($value): string
    {
        return $value ?? SurveyStatus::DRAFT->value;
    }

    public function getStatusLabelAttribute(): string
    {
        return SurveyStatus::fromString($this->status)->label();
    }

    public function getStatusColorAttribute(): string
    {
        return SurveyStatus::fromString($this->status)->color();
    }
}
