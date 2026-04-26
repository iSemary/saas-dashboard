<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\TimeManagement\Domain\ValueObjects\AttendanceStatus;
use Modules\Auth\Entities\User;

class Attendance extends Model
{
    protected $table = 'tm_attendances';

    protected $fillable = [
        'tenant_id', 'user_id', 'work_schedule_id', 'date',
        'clock_in_at', 'clock_out_at', 'worked_minutes',
        'break_minutes', 'overtime_minutes', 'status',
        'notes', 'location_data',
    ];

    protected $casts = [
        'date' => 'date',
        'clock_in_at' => 'datetime',
        'clock_out_at' => 'datetime',
        'worked_minutes' => 'integer',
        'break_minutes' => 'integer',
        'overtime_minutes' => 'integer',
        'location_data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class);
    }

    public function clockIn(?array $locationData = null): void
    {
        $this->clock_in_at = now();
        $this->status = AttendanceStatus::Present->value;
        if ($locationData) {
            $this->location_data = $locationData;
        }
        $this->save();
    }

    public function clockOut(): void
    {
        if (!$this->clock_in_at) {
            return;
        }

        $this->clock_out_at = now();
        $this->worked_minutes = (int) ($this->clock_in_at->diffInMinutes($this->clock_out_at));
        $this->save();
    }

    public function statusVO(): AttendanceStatus
    {
        return AttendanceStatus::fromString($this->status);
    }

    public function scopeForUser($query, string $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->where('date', $date);
    }
}
