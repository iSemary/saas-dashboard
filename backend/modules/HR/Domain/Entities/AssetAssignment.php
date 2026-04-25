<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'employee_id',
        'assigned_at',
        'returned_at',
        'condition_at_assignment',
        'condition_at_return',
        'notes',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];
}
