<?php

/**
 * Notification Model
 * 
 * @package Modules\Notification\Entities
 * 
 * This model represents notifications in the system.
 * 
 * IMPORTANT: This model has an observer (NotificationObserver) that triggers on creation.
 * When a notification is created, it automatically broadcasts a NotificationEvent with:
 * - title (from name field)
 * - message (from description field)
 * - type
 * 
 * @see \Modules\Notification\Observers\NotificationObserver
 * @see \Modules\Notification\Events\NotificationEvent

 * 
 * @property-read \Modules\Auth\Entities\User $user
 */

namespace Modules\Notification\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;
use Modules\FileManager\Traits\FileHandler;
use OwenIt\Auditing\Contracts\Auditable;

class Notification extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler;

    public $singleTitle = "notification";
    public $pluralTitle = "notifications";

    protected $fillable = [
        'user_id',
        // TODO remove this and add object type and object id and remove route
        'module_id',
        'name',
        'description',
        'title', // New enhanced title field
        'body', // New enhanced body field
        'type', // info, alert, announcement
        'route',
        'priority', // 'low', 'medium', 'high'
        'icon', // image
        'metadata',
        'data', // New structured data field
        'seen_at',
        'is_read', // New read status field
    ];

    protected $casts = [
        'metadata' => 'array',
        'data' => 'array',
        'seen_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    protected $fileColumns = [
        'icon' => [
            'folder' => 'notifications',
            'is_encrypted' => false,
            'access_level' => 'public',
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getIconAttribute($value)
    {
        return $this->getFileUrl($value);
    }

    /**
     * Get the notification title (fallback to name if title is null)
     */
    public function getTitleAttribute($value)
    {
        return $value ?: $this->name;
    }

    /**
     * Get the notification body (fallback to description if body is null)
     */
    public function getBodyAttribute($value)
    {
        return $value ?: $this->description;
    }

    /**
     * Scope to get unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'seen_at' => now()
        ]);
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'seen_at' => null
        ]);
    }
}
