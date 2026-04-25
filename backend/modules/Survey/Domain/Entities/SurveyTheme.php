<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class SurveyTheme extends Model
{
    use SoftDeletes;

    protected $table = 'survey_themes';

    protected $fillable = [
        'name',
        'colors',
        'font_family',
        'logo_url',
        'background_image_url',
        'button_style',
        'is_system',
        'created_by',
    ];

    protected $casts = [
        'colors' => 'array',
        'button_style' => 'array',
        'is_system' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(Survey::class, 'theme_id');
    }

    public function applyToSurvey(Survey $survey): void
    {
        $survey->update(['theme_id' => $this->id]);
    }

    public function getDefaultColors(): array
    {
        return [
            'primary' => '#6366f1',
            'secondary' => '#8b5cf6',
            'background' => '#ffffff',
            'text' => '#1f2937',
            'accent' => '#10b981',
        ];
    }

    public function getMergedColors(): array
    {
        $colors = $this->colors ?? [];
        $defaults = $this->getDefaultColors();
        return array_merge($defaults, $colors);
    }
}
