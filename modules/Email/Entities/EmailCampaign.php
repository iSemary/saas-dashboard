<?php

namespace Modules\Email\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class EmailCampaign extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable;

    protected $connection = "landlord";

    public $singleTitle = "email_campaign";
    public $pluralTitle = "email_campaigns";

    protected $fillable = [
        'campaign_id',
        'email',
        'name',
        'status',
        'opened_at',
        'clicked_at',
    ];

    protected $casts = [
        'status' => 'string',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
    ];
}
