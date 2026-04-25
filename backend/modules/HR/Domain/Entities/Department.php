<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Domain\Events\DepartmentCreated;
use Modules\HR\Domain\Events\DepartmentStatusChanged;
use Modules\HR\Domain\Exceptions\CircularDepartmentHierarchy;
use Modules\HR\Domain\Exceptions\DepartmentHasEmployees;
use Modules\HR\Domain\Exceptions\DepartmentHasSubDepartments;
use Modules\HR\Domain\Exceptions\InvalidDepartmentStatusTransition;
use Modules\HR\Domain\ValueObjects\DepartmentStatus;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'parent_id',
        'manager_id',
        'description',
        'status',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'custom_fields' => 'array',
    ];

    // Relationships
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function subDepartments(): HasMany
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    public function positions(): HasMany
    {
        return $this->hasMany(Position::class, 'department_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', DepartmentStatus::ACTIVE->value);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeByParent($query, ?int $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    // Accessors
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $current = $this->parent;
        while ($current) {
            $path[] = $current->name;
            $current = $current->parent;
        }
        return implode(' > ', array_reverse($path));
    }

    public function getLevelAttribute(): int
    {
        $level = 0;
        $current = $this->parent;
        while ($current) {
            $level++;
            $current = $current->parent;
        }
        return $level;
    }

    // Business Methods
    public function transitionStatus(DepartmentStatus $newStatus): void
    {
        if (!DepartmentStatus::canTransitionFrom($this->status, $newStatus)) {
            throw new InvalidDepartmentStatusTransition($this->status, $newStatus->value);
        }
        $oldStatus = $this->status;
        $this->update(['status' => $newStatus->value]);
        event(new DepartmentStatusChanged($this, $oldStatus, $newStatus->value));
    }

    public function canTransitionTo(DepartmentStatus $status): bool
    {
        return DepartmentStatus::canTransitionFrom($this->status, $status);
    }

    public function setParent(?int $parentId): void
    {
        // Prevent circular hierarchy
        if ($parentId && $this->wouldCreateCircularHierarchy($parentId)) {
            throw new CircularDepartmentHierarchy($this->id, $parentId);
        }
        $this->update(['parent_id' => $parentId]);
    }

    public function wouldCreateCircularHierarchy(int $potentialParentId): bool
    {
        if ($this->id === $potentialParentId) {
            return true;
        }
        $current = Department::find($potentialParentId);
        while ($current) {
            if ($current->id === $this->id) {
                return true;
            }
            $current = $current->parent;
        }
        return false;
    }

    public function canBeDeleted(): array
    {
        $errors = [];
        $employeeCount = $this->employees()->count();
        $subDeptCount = $this->subDepartments()->count();

        if ($employeeCount > 0) {
            $errors[] = "Department has {$employeeCount} employee(s)";
        }
        if ($subDeptCount > 0) {
            $errors[] = "Department has {$subDeptCount} sub-department(s)";
        }

        return $errors;
    }

    public function deleteWithCheck(): bool
    {
        $employeeCount = $this->employees()->count();
        if ($employeeCount > 0) {
            throw new DepartmentHasEmployees($this->id, $employeeCount);
        }

        $subDeptCount = $this->subDepartments()->count();
        if ($subDeptCount > 0) {
            throw new DepartmentHasSubDepartments($this->id, $subDeptCount);
        }

        return $this->delete();
    }

    public function getAllSubDepartmentIds(): array
    {
        $ids = [];
        $children = $this->subDepartments;
        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $child->getAllSubDepartmentIds());
        }
        return $ids;
    }

    // Model Events
    protected static function booted(): void
    {
        static::created(function ($model) {
            event(new DepartmentCreated($model));
        });
    }
}
