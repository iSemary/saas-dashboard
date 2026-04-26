<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceCycle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_performance_cycles';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'review_start_date',
        'review_end_date',
        'status',
        'description',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'review_start_date' => 'date',
        'review_end_date' => 'date',
    ];

    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(PerformanceReview::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'active')
            ->where('review_start_date', '<=', now())
            ->where('review_end_date', '>=', now());
    }

    public function isOpen(): bool
    {
        return $this->status === 'active'
            && now()->between($this->review_start_date, $this->review_end_date);
    }
}
