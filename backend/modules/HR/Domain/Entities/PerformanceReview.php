<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_performance_reviews';

    protected $fillable = [
        'performance_cycle_id',
        'employee_id',
        'reviewer_id',
        'self_review',
        'manager_review',
        'peer_reviews',
        'goals_achievement',
        'strengths',
        'improvements',
        'overall_rating',
        'status',
        'submitted_at',
        'created_by',
    ];

    protected $casts = [
        'self_review' => 'array',
        'manager_review' => 'array',
        'peer_reviews' => 'array',
        'overall_rating' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function performanceCycle(): BelongsTo
    {
        return $this->belongsTo(PerformanceCycle::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function submit(): void
    {
        $this->update([
            'status' => 'completed',
            'submitted_at' => now(),
        ]);
    }
}
