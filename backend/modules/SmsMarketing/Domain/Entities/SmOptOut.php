<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmOptOut extends Model
{
    protected $table = 'sm_opt_outs';

    protected $fillable = [
        'contact_id', 'campaign_id', 'reason', 'opted_out_at',
    ];

    protected $casts = [
        'opted_out_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(SmContact::class, 'contact_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(SmCampaign::class, 'campaign_id');
    }
}
