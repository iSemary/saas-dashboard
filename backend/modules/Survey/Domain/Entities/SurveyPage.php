<?php

declare(strict_types=1);

namespace Modules\Survey\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyPage extends Model
{
    protected $table = 'survey_pages';

    protected $fillable = [
        'survey_id',
        'title',
        'description',
        'order',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class, 'survey_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'page_id')->orderBy('order');
    }

    public function addQuestion(array $data): SurveyQuestion
    {
        $maxOrder = $this->questions()->max('order') ?? 0;
        $data['order'] = $maxOrder + 1;
        $data['page_id'] = $this->id;
        $data['survey_id'] = $this->survey_id;

        return $this->questions()->create($data);
    }

    public function reorderQuestions(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            $this->questions()->where('id', $id)->update(['order' => $index + 1]);
        }
    }
}
