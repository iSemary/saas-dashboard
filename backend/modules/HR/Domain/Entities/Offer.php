<?php

namespace Modules\HR\Domain\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Entities\User;

class Offer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hr_job_offers';

    protected $fillable = [
        'application_id',
        'candidate_id',
        'job_opening_id',
        'salary',
        'currency',
        'bonus',
        'benefits',
        'start_date',
        'expiry_date',
        'status', // draft, sent, accepted, rejected, expired
        'terms',
        'notes',
        'sent_at',
        'sent_by',
        'accepted_at',
        'rejected_at',
        'rejection_reason',
        'created_by',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'benefits' => 'array',
        'terms' => 'array',
        'start_date' => 'date',
        'expiry_date' => 'date',
        'sent_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function jobOpening(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function send(int $sentBy): void
    {
        $this->update([
            'status' => 'sent',
            'sent_by' => $sentBy,
            'sent_at' => now(),
        ]);
    }

    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // Emit event that will auto-create employee
        event(new \Modules\HR\Domain\Events\OfferAccepted($this));
    }

    public function reject(string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_at' => now(),
        ]);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && now()->gt($this->expiry_date);
    }
}
