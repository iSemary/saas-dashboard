<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseEnrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'employee_id',
        'enrolled_at',
        'started_at',
        'completed_at',
        'score',
        'status',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
