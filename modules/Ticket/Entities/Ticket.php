<?php

namespace Modules\Ticket\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\FileManager\Traits\FileHandler;
use Modules\Localization\Traits\Translatable;
use Modules\Comment\Entities\Comment;
use OwenIt\Auditing\Contracts\Auditable;

class Ticket extends Model implements Auditable
{
    use HasFactory, SoftDeletes, \OwenIt\Auditing\Auditable, FileHandler, Translatable;

    public $singleTitle = "ticket";
    public $pluralTitle = "tickets";

    protected $fillable = [
        'ticket_number',
        'title',
        'description',
        'html_content',
        'status',
        'priority',
        'created_by',
        'assigned_to',
        'brand_id',
        'tags',
        'due_date',
        'resolved_at',
        'closed_at',
        'sla_data',
        'metadata'
    ];

    protected $casts = [
        'tags' => 'array',
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'sla_data' => 'array',
        'metadata' => 'array'
    ];

    protected $translatableColumns = ['title', 'description'];

    /**
     * Available ticket statuses
     */
    const STATUSES = [
        'open' => 'Open',
        'in_progress' => 'In Progress',
        'on_hold' => 'On Hold',
        'resolved' => 'Resolved',
        'closed' => 'Closed'
    ];

    /**
     * Available ticket priorities
     */
    const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'urgent' => 'Urgent'
    ];

    /**
     * Status colors for UI
     */
    const STATUS_COLORS = [
        'open' => 'primary',
        'in_progress' => 'warning',
        'on_hold' => 'secondary',
        'resolved' => 'success',
        'closed' => 'dark'
    ];

    /**
     * Priority colors for UI
     */
    const PRIORITY_COLORS = [
        'low' => 'success',
        'medium' => 'info',
        'high' => 'warning',
        'urgent' => 'danger'
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }
            
            // Initialize SLA data
            $ticket->sla_data = [
                'created_at' => now(),
                'status_history' => [
                    [
                        'status' => $ticket->status,
                        'started_at' => now(),
                        'duration' => 0
                    ]
                ]
            ];
        });

        static::updating(function ($ticket) {
            if ($ticket->isDirty('status')) {
                $ticket->updateSlaData($ticket->getOriginal('status'), $ticket->status);
                
                // Set resolved/closed timestamps
                if ($ticket->status === 'resolved' && !$ticket->resolved_at) {
                    $ticket->resolved_at = now();
                }
                if ($ticket->status === 'closed' && !$ticket->closed_at) {
                    $ticket->closed_at = now();
                }
            }
        });
    }

    /**
     * Get the user who created the ticket
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    /**
     * Get the user assigned to the ticket
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'assigned_to');
    }

    /**
     * Get the brand this ticket belongs to
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(\Modules\Customer\Entities\Brand::class);
    }

    /**
     * Get the status logs for this ticket
     */
    public function statusLogs(): HasMany
    {
        return $this->hasMany(TicketStatusLog::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get comments for this ticket (polymorphic)
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable', 'object_model', 'object_id');
    }

    /**
     * Get top-level comments only
     */
    public function topLevelComments(): MorphMany
    {
        return $this->comments()->whereNull('parent_id')->orderBy('created_at', 'asc');
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $year = date('Y');
        $month = date('m');
        
        // Get the last ticket number for this month
        $lastTicket = self::where('ticket_number', 'like', "{$prefix}-{$year}{$month}%")
                         ->orderBy('ticket_number', 'desc')
                         ->first();
        
        if ($lastTicket) {
            $lastNumber = (int) substr($lastTicket->ticket_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return sprintf('%s-%s%s%04d', $prefix, $year, $month, $newNumber);
    }

    /**
     * Update SLA data when status changes
     */
    public function updateSlaData($oldStatus, $newStatus)
    {
        $slaData = $this->sla_data ?? [];
        $now = now();
        
        // Calculate time in previous status
        if (!empty($slaData['status_history'])) {
            $lastEntry = end($slaData['status_history']);
            $timeInStatus = $now->diffInSeconds($lastEntry['started_at']);
            
            // Update the last entry with duration
            $slaData['status_history'][count($slaData['status_history']) - 1]['duration'] = $timeInStatus;
        }
        
        // Add new status entry
        $slaData['status_history'][] = [
            'status' => $newStatus,
            'started_at' => $now,
            'duration' => 0
        ];
        
        $this->sla_data = $slaData;
    }

    /**
     * Get time spent in current status
     */
    public function getTimeInCurrentStatus(): int
    {
        $slaData = $this->sla_data ?? [];
        
        if (empty($slaData['status_history'])) {
            return 0;
        }
        
        $lastEntry = end($slaData['status_history']);
        return now()->diffInSeconds($lastEntry['started_at']);
    }

    /**
     * Get total time spent in a specific status
     */
    public function getTotalTimeInStatus($status): int
    {
        $slaData = $this->sla_data ?? [];
        $totalTime = 0;
        
        if (empty($slaData['status_history'])) {
            return 0;
        }
        
        foreach ($slaData['status_history'] as $entry) {
            if ($entry['status'] === $status) {
                $totalTime += $entry['duration'];
            }
        }
        
        // Add current time if currently in this status
        if ($this->status === $status) {
            $totalTime += $this->getTimeInCurrentStatus();
        }
        
        return $totalTime;
    }

    /**
     * Get formatted time in status
     */
    public function getFormattedTimeInStatus($status): string
    {
        $seconds = $this->getTotalTimeInStatus($status);
        return $this->formatDuration($seconds);
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
     * Check if ticket is overdue
     */
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && !in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        return 'badge-' . (self::STATUS_COLORS[$this->status] ?? 'secondary');
    }

    /**
     * Get priority badge class
     */
    public function getPriorityBadgeClass(): string
    {
        return 'badge-' . (self::PRIORITY_COLORS[$this->priority] ?? 'secondary');
    }

    /**
     * Scope for tickets by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for tickets by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for tickets assigned to user
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope for tickets created by user
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope for overdue tickets
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Scope for open tickets (not closed or resolved)
     */
    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', ['resolved', 'closed']);
    }

    /**
     * Get all available statuses
     */
    public static function getStatuses(): array
    {
        return self::STATUSES;
    }

    /**
     * Get all available priorities
     */
    public static function getPriorities(): array
    {
        return self::PRIORITIES;
    }
}
