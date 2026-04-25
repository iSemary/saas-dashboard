<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'audience',
        'department_ids',
        'starts_at',
        'ends_at',
        'requires_acknowledgment',
        'attachments',
        'created_by',
    ];

    protected $casts = [
        'department_ids' => 'array',
        'attachments' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'requires_acknowledgment' => 'boolean',
    ];
}
