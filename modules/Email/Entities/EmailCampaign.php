<?php

namespace Modules\Email\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmailCampaign extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'template_id',
        'name',
        'subject',
        'body',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'status' => 'string',
        'scheduled_at' => 'datetime',
    ];
}
