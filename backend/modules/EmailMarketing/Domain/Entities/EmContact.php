<?php

namespace Modules\EmailMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Event;
use Modules\EmailMarketing\Domain\ValueObjects\EmContactStatus;
use Modules\EmailMarketing\Domain\Exceptions\EmContactAlreadyUnsubscribed;
use Modules\EmailMarketing\Domain\Events\EmContactUnsubscribed;

class EmContact extends Model
{
    use SoftDeletes;

    protected $table = 'em_contacts';

    protected $fillable = [
        'email', 'first_name', 'last_name', 'phone', 'custom_fields',
        'status', 'source', 'subscribed_at', 'unsubscribed_at', 'created_by',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'status' => EmContactStatus::class,
    ];

    public function contactLists(): BelongsToMany
    {
        return $this->belongsToMany(EmContactList::class, 'em_contact_list_members', 'contact_id', 'contact_list_id');
    }

    public function sendingLogs(): HasMany
    {
        return $this->hasMany(EmSendingLog::class, 'contact_id');
    }

    public function unsubscribe(?int $campaignId = null, ?string $reason = null): void
    {
        if ($this->status === EmContactStatus::Unsubscribed) {
            throw EmContactAlreadyUnsubscribed::forEmail($this->email);
        }
        $this->status = EmContactStatus::Unsubscribed;
        $this->unsubscribed_at = now();
        Event::dispatch(new EmContactUnsubscribed($this, $campaignId, $reason));
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function isReachable(): bool
    {
        return $this->status->isReachable();
    }
}
