<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeContract extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_employee_contracts';

    protected $fillable = [
        'employee_id',
        'contract_number',
        'type',
        'status',
        'start_date',
        'end_date',
        'basic_salary',
        'currency',
        'benefits',
        'file_path',
        'notes',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'benefits' => 'array',
        'custom_fields' => 'array',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereNotNull('end_date')
            ->where('end_date', '<=', now()->addDays($days))
            ->where('end_date', '>=', now());
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->start_date <= now()
            && ($this->end_date === null || $this->end_date >= now());
    }

    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->end_date) {
            return false;
        }
        return $this->end_date->diffInDays(now()) <= $days;
    }

    public function terminate(): void
    {
        $this->update(['status' => 'terminated', 'end_date' => now()]);
    }
}
