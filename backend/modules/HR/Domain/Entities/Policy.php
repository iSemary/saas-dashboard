<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Policy extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'body',
        'version',
        'effective_from',
        'requires_acknowledgment',
        'created_by',
    ];

    protected $casts = [
        'effective_from' => 'date',
        'requires_acknowledgment' => 'boolean',
    ];
}
