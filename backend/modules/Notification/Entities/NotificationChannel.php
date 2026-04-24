<?php

namespace Modules\Notification\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Entities\User;
use OwenIt\Auditing\Contracts\Auditable;

class NotificationChannel extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public $singleTitle = "notification_channel";
    public $pluralTitle = "notification_channels";

    protected $fillable = [
        'user_id',
        'channel_type',
        'subscription_data',
        'is_active',
        'subscribed_at',
        'last_used_at',
    ];

    protected $casts = [
        'subscription_data' => 'array',
        'is_active' => 'boolean',
        'subscribed_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification channel.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope to get active channels only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get channels by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('channel_type', $type);
    }

    /**
     * Update the last used timestamp.
     */
    public function markAsUsed()
    {
        $this->update(['last_used_at' => now()]);
    }
}
