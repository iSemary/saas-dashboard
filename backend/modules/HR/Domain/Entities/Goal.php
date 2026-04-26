<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_goals';

    protected $fillable = [
        'performance_cycle_id',
        'employee_id',
        'manager_id',
        'title',
        'description',
        'category',
        'status',
        'progress',
        'weight',
        'start_date',
        'due_date',
        'completed_at',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'progress' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function performanceCycle(): BelongsTo
    {
        return $this->belongsTo(PerformanceCycle::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function keyResults(): HasMany
    {
        return $this->hasMany(KeyResult::class);
    }

    public function updateProgress(): void
    {
        $krProgress = $this->keyResults()->avg('progress') ?? 0;
        $this->progress = min(100, max(0, $krProgress));

        if ($this->progress >= 100 && !$this->completed_at) {
            $this->completed_at = now();
            $this->status = 'completed';
        }

        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'active']);
    }

    public function scopeByEmployee($query, int $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }
}
