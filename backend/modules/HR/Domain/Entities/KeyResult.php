<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeyResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_key_results';

    protected $fillable = [
        'goal_id',
        'title',
        'description',
        'target_value',
        'current_value',
        'unit',
        'progress',
        'status',
        'due_date',
        'created_by',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'progress' => 'decimal:2',
        'due_date' => 'date',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function updateProgress(float $currentValue): void
    {
        $this->current_value = $currentValue;

        if ($this->target_value > 0) {
            $this->progress = min(100, ($currentValue / $this->target_value) * 100);
        }

        if ($this->progress >= 100) {
            $this->status = 'completed';
        } elseif ($this->progress > 0) {
            $this->status = 'in_progress';
        }

        $this->save();

        // Update parent goal progress
        $this->goal->updateProgress();
    }
}
