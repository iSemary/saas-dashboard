<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certification extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'course_id',
        'name',
        'issuing_org',
        'issued_date',
        'expiry_date',
        'certificate_path',
    ];

    protected $casts = [
        'issued_date' => 'date',
        'expiry_date' => 'date',
    ];
}
