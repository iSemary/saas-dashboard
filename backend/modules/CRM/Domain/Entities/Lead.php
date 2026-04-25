<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Entities\User;
use Modules\CRM\Database\Factories\LeadFactory;
use Modules\CRM\Domain\ValueObjects\LeadStatus;
use Modules\CRM\Domain\ValueObjects\LeadSource;
use Modules\CRM\Domain\Exceptions\InvalidLeadStatusTransition;
use Modules\CRM\Domain\Exceptions\LeadAlreadyConvertedException;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'leads';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'title',
        'description',
        'status',
        'source',
        'expected_revenue',
        'expected_close_date',
        'assigned_to',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'expected_revenue' => 'decimal:2',
        'expected_close_date' => 'date',
        'custom_fields' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($lead) {
            if (empty($lead->status)) {
                $lead->status = LeadStatus::NEW->value;
            }
            if (empty($lead->source)) {
                $lead->source = LeadSource::OTHER->value;
            }
        });
    }

    // ── Relationships ─────────────────────────────────────

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    public function notes(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(CrmNote::class, 'related');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(CrmFile::class, 'related');
    }

    public function opportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class);
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('status', [
            LeadStatus::CONVERTED->value,
            LeadStatus::UNQUALIFIED->value,
        ]);
    }

    public function scopeQualified($query)
    {
        return $query->where('status', LeadStatus::QUALIFIED->value);
    }

    // ── Business Methods (Rich Domain) ────────────────────

    /**
     * Transition lead status with validation.
     *
     * @throws InvalidLeadStatusTransition
     */
    public function transitionStatus(LeadStatus $newStatus): void
    {
        $currentStatus = LeadStatus::fromString($this->status);

        if (!LeadStatus::canTransitionFrom($currentStatus, $newStatus)) {
            throw new InvalidLeadStatusTransition($this->status, $newStatus->value);
        }

        $oldStatus = $this->status;
        $this->update(['status' => $newStatus->value]);

        // Dispatch domain event
        event(new \Modules\CRM\Domain\Events\LeadStatusChanged($this, $oldStatus, $newStatus->value));
    }

    /**
     * Check if status can be transitioned to.
     */
    public function canTransitionTo(LeadStatus $status): bool
    {
        $currentStatus = LeadStatus::fromString($this->status);

        return LeadStatus::canTransitionFrom($currentStatus, $status);
    }

    /**
     * Qualify the lead.
     *
     * @throws InvalidLeadStatusTransition
     */
    public function qualify(): void
    {
        $this->transitionStatus(LeadStatus::QUALIFIED);
    }

    /**
     * Mark as contacted.
     *
     * @throws InvalidLeadStatusTransition
     */
    public function markContacted(): void
    {
        $this->transitionStatus(LeadStatus::CONTACTED);
    }

    /**
     * Mark as unqualified.
     *
     * @throws InvalidLeadStatusTransition
     */
    public function markUnqualified(): void
    {
        $this->transitionStatus(LeadStatus::UNQUALIFIED);
    }

    /**
     * Assign lead to a user.
     */
    public function assignTo(int $userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    /**
     * Convert lead to opportunity (rich domain method).
     *
     * @throws LeadAlreadyConvertedException
     */
    public function convertToOpportunity(array $opportunityData = []): Opportunity
    {
        if ($this->status === LeadStatus::CONVERTED->value) {
            throw new LeadAlreadyConvertedException($this->id);
        }

        // Check if can convert (must be qualified, contacted, or new)
        $canConvert = in_array($this->status, [
            LeadStatus::NEW->value,
            LeadStatus::CONTACTED->value,
            LeadStatus::QUALIFIED->value,
        ], true);

        if (!$canConvert) {
            throw new InvalidLeadStatusTransition(
                $this->status,
                LeadStatus::CONVERTED->value,
                'Lead must be qualified, contacted, or new to convert'
            );
        }

        return DB::transaction(function () use ($opportunityData) {
            // Create opportunity
            $opportunity = Opportunity::create(array_merge([
                'name' => $this->name,
                'lead_id' => $this->id,
                'expected_revenue' => $this->expected_revenue,
                'expected_close_date' => $this->expected_close_date,
                'assigned_to' => $this->assigned_to,
                'created_by' => auth()->id(),
                'stage' => 'prospecting',
                'contact_id' => $opportunityData['contact_id'] ?? null,
                'company_id' => $opportunityData['company_id'] ?? null,
            ], $opportunityData));

            // Update lead status
            $this->update(['status' => LeadStatus::CONVERTED->value]);

            // Dispatch domain event
            event(new \Modules\CRM\Domain\Events\LeadConverted($this, $opportunity));

            return $opportunity;
        });
    }

    /**
     * Check if lead can be converted.
     */
    public function canConvert(): bool
    {
        return in_array($this->status, [
            LeadStatus::NEW->value,
            LeadStatus::CONTACTED->value,
            LeadStatus::QUALIFIED->value,
        ], true);
    }

    /**
     * Check if lead is converted.
     */
    public function isConverted(): bool
    {
        return $this->status === LeadStatus::CONVERTED->value;
    }

    /**
     * Check if lead is qualified.
     */
    public function isQualified(): bool
    {
        return $this->status === LeadStatus::QUALIFIED->value;
    }

    /**
     * Check if lead is new.
     */
    public function isNew(): bool
    {
        return $this->status === LeadStatus::NEW->value;
    }

    // ── Value Object Accessors ───────────────────────────

    public function getStatusAttribute($value): string
    {
        return $value ?? LeadStatus::NEW->value;
    }

    public function getStatusLabelAttribute(): string
    {
        return LeadStatus::fromString($this->status)->label();
    }

    public function getStatusColorAttribute(): string
    {
        return LeadStatus::fromString($this->status)->color();
    }

    public function getSourceLabelAttribute(): string
    {
        return LeadSource::fromString($this->source)->label();
    }

    // ── Factory ───────────────────────────────────────────

    protected static function newFactory()
    {
        return LeadFactory::new();
    }
}
