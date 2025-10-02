<?php

namespace Modules\Ticket\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class TicketStatusLog extends Model implements Auditable
{
    use HasFactory, \OwenIt\Auditing\Auditable;

    public $singleTitle = "ticket_status_log";
    public $pluralTitle = "ticket_status_logs";

    protected $fillable = [
        'ticket_id',
        'old_status',
        'new_status',
        'changed_by',
        'comment',
        'time_in_previous_status',
        'metadata'
    ];

    protected $casts = [
        'time_in_previous_status' => 'integer',
        'metadata' => 'array'
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            // Calculate time in previous status if old_status exists
            if ($log->old_status && $log->ticket_id) {
                $previousLog = self::where('ticket_id', $log->ticket_id)
                                  ->where('new_status', $log->old_status)
                                  ->orderBy('created_at', 'desc')
                                  ->first();
                
                if ($previousLog) {
                    $log->time_in_previous_status = now()->diffInSeconds($previousLog->created_at);
                }
            }
        });
    }

    /**
     * Get the ticket this log belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who changed the status
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'changed_by');
    }

    /**
     * Get formatted time in previous status
     */
    public function getFormattedTimeInPreviousStatusAttribute(): string
    {
        if (!$this->time_in_previous_status) {
            return 'N/A';
        }

        return $this->formatDuration($this->time_in_previous_status);
    }

    /**
     * Format duration in human readable format
     */
    public function formatDuration($seconds): string
    {
        if ($seconds < 60) {
            return $seconds . 's';
        } elseif ($seconds < 3600) {
            return round($seconds / 60) . 'm';
        } elseif ($seconds < 86400) {
            return round($seconds / 3600, 1) . 'h';
        } else {
            return round($seconds / 86400, 1) . 'd';
        }
    }

    /**
     * Get status change description
     */
    public function getStatusChangeDescriptionAttribute(): string
    {
        if (!$this->old_status) {
            return "Ticket created with status: " . ucfirst(str_replace('_', ' ', $this->new_status));
        }

        $oldStatus = ucfirst(str_replace('_', ' ', $this->old_status));
        $newStatus = ucfirst(str_replace('_', ' ', $this->new_status));
        
        return "Status changed from {$oldStatus} to {$newStatus}";
    }

    /**
     * Get status badge class for old status
     */
    public function getOldStatusBadgeClass(): string
    {
        if (!$this->old_status) {
            return 'badge-secondary';
        }
        
        return 'badge-' . (Ticket::STATUS_COLORS[$this->old_status] ?? 'secondary');
    }

    /**
     * Get status badge class for new status
     */
    public function getNewStatusBadgeClass(): string
    {
        return 'badge-' . (Ticket::STATUS_COLORS[$this->new_status] ?? 'secondary');
    }

    /**
     * Check if this is the initial status (ticket creation)
     */
    public function isInitialStatus(): bool
    {
        return is_null($this->old_status);
    }

    /**
     * Check if status was changed to resolved
     */
    public function isResolved(): bool
    {
        return $this->new_status === 'resolved';
    }

    /**
     * Check if status was changed to closed
     */
    public function isClosed(): bool
    {
        return $this->new_status === 'closed';
    }

    /**
     * Check if status was reopened (from resolved/closed to open/in_progress)
     */
    public function isReopened(): bool
    {
        return in_array($this->old_status, ['resolved', 'closed']) && 
               in_array($this->new_status, ['open', 'in_progress']);
    }

    /**
     * Scope for logs of a specific ticket
     */
    public function scopeForTicket($query, $ticketId)
    {
        return $query->where('ticket_id', $ticketId);
    }

    /**
     * Scope for logs by a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('changed_by', $userId);
    }

    /**
     * Scope for logs with specific status change
     */
    public function scopeStatusChange($query, $fromStatus, $toStatus)
    {
        return $query->where('old_status', $fromStatus)
                    ->where('new_status', $toStatus);
    }

    /**
     * Scope for recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Create a status log entry
     */
    public static function logStatusChange($ticketId, $oldStatus, $newStatus, $changedBy, $comment = null)
    {
        return self::create([
            'ticket_id' => $ticketId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'comment' => $comment
        ]);
    }

    /**
     * Get timeline data for a ticket
     */
    public static function getTicketTimeline($ticketId)
    {
        return self::where('ticket_id', $ticketId)
                  ->with('changedBy')
                  ->orderBy('created_at', 'asc')
                  ->get()
                  ->map(function ($log) {
                      return [
                          'id' => $log->id,
                          'type' => 'status_change',
                          'title' => $log->status_change_description,
                          'description' => $log->comment,
                          'user' => $log->changedBy->name ?? 'System',
                          'user_avatar' => $log->changedBy->avatar ?? null,
                          'timestamp' => $log->created_at,
                          'old_status' => $log->old_status,
                          'new_status' => $log->new_status,
                          'time_in_previous' => $log->formatted_time_in_previous_status,
                          'badge_class' => $log->new_status_badge_class
                      ];
                  });
    }
}
