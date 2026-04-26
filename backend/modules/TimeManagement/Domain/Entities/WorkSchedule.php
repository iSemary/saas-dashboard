<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class WorkSchedule extends Model
{
    protected $table = 'tm_work_schedules';

    protected $fillable = [
        'tenant_id', 'user_id', 'work_calendar_id',
        'shift_template_id', 'effective_from', 'effective_to', 'overrides',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'effective_to' => 'date',
        'overrides' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workCalendar(): BelongsTo
    {
        return $this->belongsTo(WorkCalendar::class);
    }

    public function shiftTemplate(): BelongsTo
    {
        return $this->belongsTo(ShiftTemplate::class);
    }
}
