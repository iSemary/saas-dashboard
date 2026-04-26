<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    use SoftDeletes;

    protected $table = 'hr_holidays';

    protected $fillable = [
        'name',
        'date',
        'country',
        'is_recurring',
        'applies_to_all_departments',
        'department_ids',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'is_recurring' => 'boolean',
        'applies_to_all_departments' => 'boolean',
        'department_ids' => 'array',
    ];

    public function isApplicableToDepartment(int $departmentId): bool
    {
        if ($this->applies_to_all_departments) {
            return true;
        }
        return in_array($departmentId, $this->department_ids ?? []);
    }
}
