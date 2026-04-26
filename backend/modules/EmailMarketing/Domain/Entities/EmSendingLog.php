<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\EmailMarketing\Domain\ValueObjects\EmLogStatus;

class EmSendingLog extends Model
{
    protected $table = 'em_sending_logs';

    protected $fillable = [
        'campaign_id', 'contact_id', 'status', 'sent_at', 'opened_at',
        'clicked_at', 'failed_reason', 'message_id', 'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'metadata' => 'array',
        'status' => EmLogStatus::class,
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmCampaign::class, 'campaign_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(EmContact::class, 'contact_id');
    }
}
