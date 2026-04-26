<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnboardingTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'hr_onboarding_templates';

    protected $fillable = [
        'name',
        'type',
        'department_id',
        'is_active',
        'created_by',
    ];
}
