<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Event;
use Modules\EmailMarketing\Domain\ValueObjects\EmCampaignStatus;
use Modules\EmailMarketing\Domain\Exceptions\InvalidEmCampaignTransition;
use Modules\EmailMarketing\Domain\Events\EmCampaignStatusChanged;
use Modules\EmailMarketing\Domain\Events\EmCampaignSent;

class EmCampaign extends Model
{
    use SoftDeletes;

    protected $table = 'em_campaigns';

    protected $fillable = [
        'name', 'subject', 'template_id', 'credential_id', 'from_name', 'from_email',
        'body_html', 'body_text', 'status', 'scheduled_at', 'sent_at', 'ab_test_id',
        'settings', 'stats', 'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'settings' => 'array',
        'stats' => 'array',
        'status' => EmCampaignStatus::class,
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(EmTemplate::class, 'template_id');
    }

    public function credential(): BelongsTo
    {
        return $this->belongsTo(EmCredential::class, 'credential_id');
    }

    public function contactLists(): BelongsToMany
    {
        return $this->belongsToMany(EmContactList::class, 'em_campaign_lists', 'campaign_id', 'contact_list_id');
    }

    public function sendingLogs(): HasMany
    {
        return $this->hasMany(EmSendingLog::class, 'campaign_id');
    }

    public function transitionTo(EmCampaignStatus $newStatus): void
    {
        $current = $this->status;
        if (! $current->canTransitionTo($newStatus)) {
            throw InvalidEmCampaignTransition::from($current, $newStatus);
        }
        $this->status = $newStatus;

        if ($newStatus === EmCampaignStatus::Sent) {
            $this->sent_at = now();
        }

        $this->save();

        Event::dispatch(new EmCampaignStatusChanged($this, $current, $newStatus));

        if ($newStatus === EmCampaignStatus::Sent) {
            Event::dispatch(new EmCampaignSent($this));
        }
    }

    public function isDraft(): bool
    {
        return $this->status === EmCampaignStatus::Draft;
    }

    public function isSent(): bool
    {
        return $this->status === EmCampaignStatus::Sent;
    }

    public function isSendable(): bool
    {
        return in_array($this->status, [EmCampaignStatus::Draft, EmCampaignStatus::Scheduled]);
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [EmCampaignStatus::Draft, EmCampaignStatus::Scheduled, EmCampaignStatus::Paused]);
    }
}
