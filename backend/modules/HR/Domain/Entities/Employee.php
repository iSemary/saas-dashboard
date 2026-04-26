<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Domain\Events\EmployeeCreated;
use Modules\HR\Domain\Events\EmployeeDepartmentChanged;
use Modules\HR\Domain\Events\EmployeePositionChanged;
use Modules\HR\Domain\Events\EmployeePromoted;
use Modules\HR\Domain\Events\EmployeeTerminated;
use Modules\HR\Domain\Events\EmploymentStatusChanged;
use Modules\HR\Domain\Exceptions\InvalidEmploymentStatusTransition;
use Modules\HR\Domain\ValueObjects\EmploymentStatus;
use Modules\HR\Domain\ValueObjects\EmploymentType;
use Modules\HR\Domain\ValueObjects\Gender;
use Modules\HR\Domain\ValueObjects\MaritalStatus;
use Modules\HR\Domain\ValueObjects\PayFrequency;
use Modules\Customer\Entities\Tenant\Brand;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function scopeForBrand($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    protected $table = 'hr_employees';

    protected $fillable = [
        'employee_number',
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'marital_status',
        'national_id',
        'passport_number',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'hire_date',
        'probation_end_date',
        'termination_date',
        'employment_status',
        'employment_type',
        'department_id',
        'position_id',
        'manager_id',
        'salary',
        'currency',
        'pay_frequency',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
        'avatar',
        'created_by',
        'brand_id',
        'custom_fields',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'probation_end_date' => 'date',
        'termination_date' => 'date',
        'salary' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function employmentHistory(): HasMany
    {
        return $this->hasMany(EmploymentHistory::class)->orderBy('effective_date', 'desc');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('employment_status', EmploymentStatus::ACTIVE->value);
    }

    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByManager($query, int $managerId)
    {
        return $query->where('manager_id', $managerId);
    }

    public function scopeByPosition($query, int $positionId)
    {
        return $query->where('position_id', $positionId);
    }

    public function scopeTerminated($query)
    {
        return $query->where('employment_status', EmploymentStatus::TERMINATED->value);
    }

    public function scopeOnProbation($query)
    {
        return $query->where('employment_status', EmploymentStatus::PROBATION->value);
    }

    // Accessors
    public function getFullNameAttribute(): string
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return implode(' ', $parts);
    }

    public function getDisplayNameAttribute(): string
    {
        return "#{$this->employee_number} - {$this->full_name}";
    }

    public function getIsActiveAttribute(): bool
    {
        $status = EmploymentStatus::tryFrom($this->employment_status);
        return $status?->isActive() ?? false;
    }

    public function getYearsOfServiceAttribute(): ?int
    {
        if (!$this->hire_date) {
            return null;
        }
        $endDate = $this->termination_date ?? now();
        return $this->hire_date->diffInYears($endDate);
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->diffInYears(now());
    }

    // Business Methods
    public function transitionStatus(EmploymentStatus $newStatus, ?string $reason = null, ?\DateTimeInterface $effectiveDate = null): void
    {
        if (!EmploymentStatus::canTransitionFrom($this->employment_status, $newStatus)) {
            throw new InvalidEmploymentStatusTransition($this->employment_status, $newStatus->value);
        }

        $oldStatus = $this->employment_status;
        $updateData = ['employment_status' => $newStatus->value];

        if ($newStatus === EmploymentStatus::TERMINATED) {
            $updateData['termination_date'] = $effectiveDate ?? now();
        }

        $this->update($updateData);

        $this->recordHistory('employment_status', $oldStatus, $newStatus->value, $reason, $effectiveDate);

        event(new EmploymentStatusChanged($this, $oldStatus, $newStatus->value, $reason));

        if ($newStatus === EmploymentStatus::TERMINATED) {
            event(new EmployeeTerminated($this, $reason, $effectiveDate));
        }
    }

    public function canTransitionTo(EmploymentStatus $status): bool
    {
        return EmploymentStatus::canTransitionFrom($this->employment_status, $status);
    }

    public function transfer(int $newDepartmentId, ?int $newPositionId = null, ?string $reason = null, ?\DateTimeInterface $effectiveDate = null): void
    {
        $oldDepartmentId = $this->department_id;
        $oldPositionId = $this->position_id;

        $updateData = ['department_id' => $newDepartmentId];
        if ($newPositionId !== null) {
            $updateData['position_id'] = $newPositionId;
        }

        $this->update($updateData);

        if ($oldDepartmentId !== $newDepartmentId) {
            $this->recordHistory('department_id', $oldDepartmentId, $newDepartmentId, $reason, $effectiveDate);
            event(new EmployeeDepartmentChanged($this, $oldDepartmentId, $newDepartmentId, $reason));
        }

        if ($newPositionId !== null && $oldPositionId !== $newPositionId) {
            $this->recordHistory('position_id', $oldPositionId, $newPositionId, $reason, $effectiveDate);
            event(new EmployeePositionChanged($this, $oldPositionId, $newPositionId, $reason));
        }
    }

    public function promote(int $newPositionId, ?float $newSalary = null, ?string $reason = null, ?\DateTimeInterface $effectiveDate = null): void
    {
        $oldPositionId = $this->position_id;
        $oldSalary = $this->salary;

        $updateData = ['position_id' => $newPositionId];
        if ($newSalary !== null) {
            $updateData['salary'] = $newSalary;
        }

        $this->update($updateData);

        $this->recordHistory('position_id', $oldPositionId, $newPositionId, $reason, $effectiveDate);
        if ($newSalary !== null) {
            $this->recordHistory('salary', $oldSalary, $newSalary, $reason, $effectiveDate);
        }

        event(new EmployeePromoted($this, $oldPositionId, $newPositionId, $newSalary, $reason));
    }

    public function updateSalary(float $newSalary, ?string $reason = null, ?\DateTimeInterface $effectiveDate = null): void
    {
        $oldSalary = $this->salary;
        $this->update(['salary' => $newSalary]);
        $this->recordHistory('salary', $oldSalary, $newSalary, $reason, $effectiveDate);
    }

    public function assignManager(?int $managerId): void
    {
        // Prevent circular management
        if ($managerId && $this->wouldCreateCircularManagement($managerId)) {
            throw new \RuntimeException("Cannot assign employee as their own indirect manager");
        }
        $this->update(['manager_id' => $managerId]);
    }

    public function wouldCreateCircularManagement(int $potentialManagerId): bool
    {
        if ($this->id === $potentialManagerId) {
            return true;
        }
        $current = Employee::find($potentialManagerId);
        $depth = 0;
        $maxDepth = 20; // Prevent infinite loops
        while ($current && $depth < $maxDepth) {
            if ($current->manager_id === $this->id) {
                return true;
            }
            $current = $current->manager;
            $depth++;
        }
        return false;
    }

    public function terminate(?string $reason = null, ?\DateTimeInterface $terminationDate = null): void
    {
        $this->transitionStatus(EmploymentStatus::TERMINATED, $reason, $terminationDate);
    }

    public function reactivate(?string $reason = null): void
    {
        $this->transitionStatus(EmploymentStatus::ACTIVE, $reason);
    }

    public function recordHistory(string $changeType, $fromValue, $toValue, ?string $notes = null, ?\DateTimeInterface $effectiveDate = null): EmploymentHistory
    {
        return $this->employmentHistory()->create([
            'change_type' => $changeType,
            'from_value' => $fromValue,
            'to_value' => $toValue,
            'effective_date' => $effectiveDate ?? now(),
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    public function getAllSubordinateIds(): array
    {
        $ids = [];
        $children = $this->subordinates;
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllSubordinateIds());
        }
        return $ids;
    }

    public function isManager(): bool
    {
        return $this->subordinates()->exists();
    }

    public function getCurrentContract(): ?EmployeeContract
    {
        return $this->contracts()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->first();
    }

    public function getLatestDocument(string $type): ?EmployeeDocument
    {
        return $this->documents()
            ->where('type', $type)
            ->latest('issued_date')
            ->first();
    }

    // Model Events
    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->employee_number)) {
                $model->employee_number = static::generateEmployeeNumber();
            }
        });

        static::created(function ($model) {
            event(new EmployeeCreated($model));
        });
    }

    protected static function generateEmployeeNumber(): string
    {
        $prefix = 'EMP';
        $year = now()->format('Y');
        $count = static::whereYear('created_at', now()->year)->count() + 1;
        return "{$prefix}{$year}" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
