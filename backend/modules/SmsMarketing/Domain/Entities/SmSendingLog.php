<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\SmsMarketing\Domain\ValueObjects\SmLogStatus;

class SmSendingLog extends Model
{
    protected $table = 'sm_sending_logs';

    protected $fillable = [
        'campaign_id', 'contact_id', 'status', 'sent_at', 'delivered_at',
        'failed_reason', 'provider_message_id', 'cost', 'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'metadata' => 'array',
        'status' => SmLogStatus::class,
        'cost' => 'decimal:6',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(SmCampaign::class, 'campaign_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(SmContact::class, 'contact_id');
    }
}
