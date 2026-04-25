<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmploymentHistory extends Model
{
    use HasFactory;

    protected $table = 'employment_history';

    protected $fillable = [
        'employee_id',
        'change_type',
        'from_value',
        'to_value',
        'effective_date',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'effective_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }

    public function scopeByChangeType($query, string $type)
    {
        return $query->where('change_type', $type);
    }

    public function getChangeTypeLabelAttribute(): string
    {
        return match($this->change_type) {
            'department_id' => 'Department Change',
            'position_id' => 'Position Change',
            'employment_status' => 'Status Change',
            'salary' => 'Salary Change',
            'manager_id' => 'Manager Change',
            default => ucfirst(str_replace('_', ' ', $this->change_type)),
        };
    }
}
