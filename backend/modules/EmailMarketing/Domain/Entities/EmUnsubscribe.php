<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmUnsubscribe extends Model
{
    protected $table = 'em_unsubscribes';

    protected $fillable = [
        'contact_id', 'campaign_id', 'reason', 'unsubscribed_at',
    ];

    protected $casts = [
        'unsubscribed_at' => 'datetime',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(EmContact::class, 'contact_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmCampaign::class, 'campaign_id');
    }
}
