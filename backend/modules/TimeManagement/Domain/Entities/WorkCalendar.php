<?php

declare(strict_types=1);

namespace Modules\TimeManagement\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class WorkCalendar extends Model
{
    use SoftDeletes;

    protected $table = 'tm_work_calendars';

    protected $fillable = [
        'tenant_id', 'name', 'description', 'timezone',
        'working_days', 'holidays', 'default_start_time',
        'default_end_time', 'default_break_minutes',
        'is_default', 'created_by',
    ];

    protected $casts = [
        'working_days' => 'array',
        'holidays' => 'array',
        'is_default' => 'boolean',
        'default_break_minutes' => 'integer',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(WorkSchedule::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isWorkingDay(string $date): bool
    {
        $dayOfWeek = (int) date('N', strtotime($date));
        return in_array($dayOfWeek, $this->working_days ?? []);
    }
}
