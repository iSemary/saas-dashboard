<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Event;
use Modules\SmsMarketing\Domain\ValueObjects\SmCampaignStatus;
use Modules\SmsMarketing\Domain\Exceptions\InvalidSmCampaignTransition;
use Modules\SmsMarketing\Domain\Events\SmCampaignStatusChanged;
use Modules\SmsMarketing\Domain\Events\SmCampaignSent;

class SmCampaign extends Model
{
    use SoftDeletes;

    protected $table = 'sm_campaigns';

    protected $fillable = [
        'name', 'template_id', 'credential_id', 'body', 'status',
        'scheduled_at', 'sent_at', 'ab_test_id', 'settings', 'stats', 'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'settings' => 'array',
        'stats' => 'array',
        'status' => SmCampaignStatus::class,
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(SmTemplate::class, 'template_id');
    }

    public function credential(): BelongsTo
    {
        return $this->belongsTo(SmCredential::class, 'credential_id');
    }

    public function contactLists(): BelongsToMany
    {
        return $this->belongsToMany(SmContactList::class, 'sm_campaign_lists', 'campaign_id', 'contact_list_id');
    }

    public function sendingLogs(): HasMany
    {
        return $this->hasMany(SmSendingLog::class, 'campaign_id');
    }

    public function transitionTo(SmCampaignStatus $newStatus): void
    {
        $current = $this->status;
        if (! $current->canTransitionTo($newStatus)) {
            throw InvalidSmCampaignTransition::from($current, $newStatus);
        }
        $this->status = $newStatus;

        if ($newStatus === SmCampaignStatus::Sent) {
            $this->sent_at = now();
        }

        $this->save();

        Event::dispatch(new SmCampaignStatusChanged($this, $current, $newStatus));

        if ($newStatus === SmCampaignStatus::Sent) {
            Event::dispatch(new SmCampaignSent($this));
        }
    }

    public function isDraft(): bool
    {
        return $this->status === SmCampaignStatus::Draft;
    }

    public function isSent(): bool
    {
        return $this->status === SmCampaignStatus::Sent;
    }

    public function isSendable(): bool
    {
        return in_array($this->status, [SmCampaignStatus::Draft, SmCampaignStatus::Scheduled]);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [SmCampaignStatus::Draft, SmCampaignStatus::Scheduled, SmCampaignStatus::Paused]);
    }
}
