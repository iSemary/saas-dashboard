<?php

namespace Modules\SmsMarketing\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Event;
use Modules\SmsMarketing\Domain\ValueObjects\SmContactStatus;
use Modules\SmsMarketing\Domain\Exceptions\SmContactAlreadyOptedOut;
use Modules\SmsMarketing\Domain\Events\SmContactOptedOut;

class SmContact extends Model
{
    use SoftDeletes;

    protected $table = 'sm_contacts';

    protected $fillable = [
        'phone', 'first_name', 'last_name', 'email', 'custom_fields',
        'status', 'source', 'opted_in_at', 'opted_out_at', 'created_by',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'opted_in_at' => 'datetime',
        'opted_out_at' => 'datetime',
        'status' => SmContactStatus::class,
    ];

    public function contactLists(): BelongsToMany
    {
        return $this->belongsToMany(SmContactList::class, 'sm_contact_list_members', 'contact_id', 'contact_list_id');
    }

    public function sendingLogs(): HasMany
    {
        return $this->hasMany(SmSendingLog::class, 'contact_id');
    }

    public function optOut(?int $campaignId = null, ?string $reason = null): void
    {
        if ($this->status === SmContactStatus::OptedOut) {
            throw SmContactAlreadyOptedOut::forPhone($this->phone);
        }
        $this->status = SmContactStatus::OptedOut;
        $this->opted_out_at = now();
        Event::dispatch(new SmContactOptedOut($this, $campaignId, $reason));
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
