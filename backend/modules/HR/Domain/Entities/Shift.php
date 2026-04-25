<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Shift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'start_time',
        'end_time',
        'break_minutes',
        'working_days',
        'grace_minutes',
        'is_night_shift',
        'is_active',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'working_days' => 'array',
        'break_minutes' => 'integer',
        'grace_minutes' => 'integer',
        'is_night_shift' => 'boolean',
        'is_active' => 'boolean',
        'custom_fields' => 'array',
    ];

    public function workSchedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getDurationHours(): float
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        if ($end->lessThan($start)) {
            $end->addDay();
        }
        return $start->diffInMinutes($end) / 60;
    }

    public function getWorkingHours(): float
    {
        return $this->getDurationHours() - ($this->break_minutes / 60);
    }
}
