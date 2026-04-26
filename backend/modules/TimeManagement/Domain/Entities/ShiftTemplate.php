<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class ShiftTemplate extends Model
{
    protected $table = 'tm_shift_templates';

    protected $fillable = [
        'tenant_id', 'name', 'start_time', 'end_time',
        'break_minutes', 'color', 'created_by',
    ];

    protected $casts = [
        'break_minutes' => 'integer',
    ];

    public function schedules()
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function durationMinutes(): int
    {
        $start = strtotime($this->start_time);
        $end = strtotime($this->end_time);
        if ($end <= $start) {
            $end += 86400; // overnight shift
        }
        return (int) (($end - $start) / 60) - $this->break_minutes;
    }
}
