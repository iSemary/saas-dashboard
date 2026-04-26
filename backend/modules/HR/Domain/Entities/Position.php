<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\HR\Domain\Events\PositionCreated;
use Modules\HR\Domain\ValueObjects\PositionLevel;
use Modules\Customer\Entities\Tenant\Brand;

class Position extends Model
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

    protected $table = 'hr_positions';

    protected $fillable = [
        'title',
        'code',
        'department_id',
        'level',
        'min_salary',
        'max_salary',
        'description',
        'requirements',
        'is_active',
        'created_by',
        'brand_id',
        'custom_fields',
    ];

    protected $casts = [
        'min_salary' => 'decimal:2',
        'max_salary' => 'decimal:2',
        'is_active' => 'boolean',
        'custom_fields' => 'array',
    ];

    // Relationships
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'position_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeByLevel($query, PositionLevel $level)
    {
        return $query->where('level', $level->value);
    }

    // Accessors
    public function getLevelLabelAttribute(): string
    {
        return PositionLevel::tryFrom($this->level)?->label() ?? $this->level;
    }

    public function getFullTitleAttribute(): string
    {
        $dept = $this->department;
        return $dept ? "{$this->title} ({$dept->name})" : $this->title;
    }

    public function getEmployeeCountAttribute(): int
    {
        return $this->employees()->count();
    }

    // Business Methods
    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function isWithinSalaryRange(float $salary): bool
    {
        if ($this->min_salary === null && $this->max_salary === null) {
            return true;
        }
        if ($this->min_salary !== null && $salary < $this->min_salary) {
            return false;
        }
        if ($this->max_salary !== null && $salary > $this->max_salary) {
            return false;
        }
        return true;
    }

    public function hasEmployees(): bool
    {
        return $this->employees()->exists();
    }

    // Model Events
    protected static function booted(): void
    {
        static::created(function ($model) {
            event(new PositionCreated($model));
        });
    }
}
