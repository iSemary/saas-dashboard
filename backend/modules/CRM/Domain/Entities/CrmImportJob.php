<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

/**
 * CSV import job for CRM data.
 */
class CrmImportJob extends Model
{
    protected $table = 'crm_import_jobs';

    protected $fillable = [
        'entity_type',
        'status',
        'file_path',
        'mapping',
        'total_rows',
        'processed_rows',
        'failed_rows',
        'error_log',
        'settings',
        'created_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'mapping' => 'array',
        'error_log' => 'array',
        'settings' => 'array',
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'failed_rows' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the user who created this import job.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope: Get pending jobs.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope: Get processing jobs.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope: Get completed jobs.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope: Get failed jobs.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Business method: Mark as processing.
     */
    public function markProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    /**
     * Business method: Mark as completed.
     */
    public function markCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Business method: Mark as failed.
     */
    public function markFailed(string $error): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'error_log' => array_merge($this->error_log ?? [], ['fatal' => $error]),
        ]);
    }

    /**
     * Update progress.
     */
    public function updateProgress(int $processed, int $failed = 0): void
    {
        $this->update([
            'processed_rows' => $processed,
            'failed_rows' => $failed,
        ]);
    }

    /**
     * Log an error for a specific row.
     */
    public function logRowError(int $rowNumber, string $error): void
    {
        $errors = $this->error_log ?? [];
        $errors['rows'][$rowNumber] = $error;

        $this->update(['error_log' => $errors]);
    }

    /**
     * Calculate progress percentage.
     */
    public function progressPercentage(): int
    {
        if ($this->total_rows === 0) {
            return 0;
        }

        return (int) round(($this->processed_rows / $this->total_rows) * 100);
    }

    /**
     * Check if import is complete.
     */
    public function isComplete(): bool
    {
        return in_array($this->status, ['completed', 'failed'], true);
    }

    /**
     * Get processing duration in seconds.
     */
    public function duration(): ?int
    {
        if (!$this->started_at) {
            return null;
        }

        $end = $this->completed_at ?? now();

        return (int) $this->started_at->diffInSeconds($end);
    }

    /**
     * Get formatted duration.
     */
    public function formattedDuration(): string
    {
        $seconds = $this->duration();

        if ($seconds === null) {
            return 'Not started';
        }

        if ($seconds < 60) {
            return $seconds . 's';
        }

        $minutes = floor($seconds / 60);
        $remaining = $seconds % 60;

        return $minutes . 'm ' . $remaining . 's';
    }

    /**
     * Get success rate percentage.
     */
    public function successRate(): int
    {
        if ($this->processed_rows === 0) {
            return 0;
        }

        $successful = $this->processed_rows - $this->failed_rows;

        return (int) round(($successful / $this->processed_rows) * 100);
    }
}
