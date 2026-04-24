<?php

namespace Modules\CRM\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Auth\Entities\User;

class Activity extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'subject',
        'description',
        'type',
        'status',
        'due_date',
        'completed_at',
        'related_id',
        'related_type',
        'assigned_to',
        'created_by',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    /**
     * Get the user assigned to this activity.
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created this activity.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the related model (polymorphic).
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope for filtering by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by assigned user.
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope for overdue activities.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'completed');
    }

    /**
     * Scope for upcoming activities.
     */
    public function scopeUpcoming($query, $days = 7)
    {
        return $query->whereBetween('due_date', [now(), now()->addDays($days)])
                    ->where('status', '!=', 'completed');
    }

    /**
     * Mark activity as completed.
     */
    public function markCompleted()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Check if activity is overdue.
     */
    public function isOverdue()
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               $this->status !== 'completed';
    }

    /**
     * Check if activity is upcoming.
     */
    public function isUpcoming($days = 7)
    {
        return $this->due_date && 
               $this->due_date->isFuture() && 
               $this->due_date->diffInDays(now()) <= $days &&
               $this->status !== 'completed';
    }
}
