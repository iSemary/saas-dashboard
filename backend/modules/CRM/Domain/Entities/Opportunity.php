<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;
use Modules\CRM\Database\Factories\OpportunityFactory;
use Modules\CRM\Domain\ValueObjects\OpportunityStage;
use Modules\CRM\Domain\ValueObjects\Money;
use Modules\CRM\Domain\Exceptions\InvalidPipelineStageTransition;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'opportunities';

    protected $fillable = [
        'name',
        'lead_id',
        'contact_id',
        'company_id',
        'stage',
        'probability',
        'expected_revenue',
        'expected_close_date',
        'actual_close_date',
        'assigned_to',
        'created_by',
        'custom_fields',
        'description',
    ];

    protected $casts = [
        'probability' => 'decimal:2',
        'expected_revenue' => 'decimal:2',
        'expected_close_date' => 'date',
        'actual_close_date' => 'date',
        'custom_fields' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($opportunity) {
            if (empty($opportunity->stage)) {
                $opportunity->stage = OpportunityStage::PROSPECTING->value;
            }
            if (empty($opportunity->probability)) {
                $opportunity->probability = OpportunityStage::PROSPECTING->probability();
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

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
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

    // ── Scopes ─────────────────────────────────────────────

    public function scopeByStage($query, $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeOpen($query)
    {
        return $query->whereNotIn('stage', ['closed_won', 'closed_lost']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('stage', ['closed_won', 'closed_lost']);
    }

    public function scopeWon($query)
    {
        return $query->where('stage', 'closed_won');
    }

    public function scopeOverdue($query)
    {
        return $query->where('expected_close_date', '<', now())
            ->whereNotIn('stage', ['closed_won', 'closed_lost']);
    }

    // ── Business Methods ─────────────────────────────────

    /**
     * Move to a new stage with validation.
     *
     * @throws InvalidPipelineStageTransition
     */
    public function moveToStage(OpportunityStage $newStage): void
    {
        $currentStage = OpportunityStage::fromString($this->stage);

        // Terminal stages cannot transition out
        if ($currentStage->isTerminal()) {
            throw new InvalidPipelineStageTransition(
                $this->stage,
                $newStage->value,
                'Cannot transition from a closed stage'
            );
        }

        $oldStage = $this->stage;

        $this->update([
            'stage' => $newStage->value,
            'probability' => $newStage->probability(),
        ]);

        // Dispatch domain event
        event(new \Modules\CRM\Domain\Events\OpportunityStageChanged($this, $oldStage, $newStage->value));

        // If closing, set actual close date
        if ($newStage->isTerminal()) {
            $this->update(['actual_close_date' => now()]);

            if ($newStage === OpportunityStage::CLOSED_WON) {
                event(new \Modules\CRM\Domain\Events\OpportunityClosedWon($this));
            } else {
                event(new \Modules\CRM\Domain\Events\OpportunityClosedLost($this));
            }
        }
    }

    /**
     * Close as won.
     */
    public function closeWon(): void
    {
        $this->moveToStage(OpportunityStage::CLOSED_WON);
    }

    /**
     * Close as lost.
     */
    public function closeLost(): void
    {
        $this->moveToStage(OpportunityStage::CLOSED_LOST);
    }

    /**
     * Assign to user.
     */
    public function assignTo(int $userId): void
    {
        $this->update(['assigned_to' => $userId]);
    }

    /**
     * Calculate weighted revenue.
     */
    public function weightedRevenue(): float
    {
        return $this->expected_revenue * ($this->probability / 100);
    }

    /**
     * Check if opportunity is open.
     */
    public function isOpen(): bool
    {
        return !OpportunityStage::fromString($this->stage)->isTerminal();
    }

    /**
     * Check if opportunity is closed won.
     */
    public function isWon(): bool
    {
        return $this->stage === OpportunityStage::CLOSED_WON->value;
    }

    /**
     * Check if opportunity is closed lost.
     */
    public function isLost(): bool
    {
        return $this->stage === OpportunityStage::CLOSED_LOST->value;
    }

    /**
     * Check if opportunity is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->isOpen() &&
               $this->expected_close_date &&
               $this->expected_close_date->isPast();
    }

    // ── Accessors ─────────────────────────────────────────

    public function getStageLabelAttribute(): string
    {
        return OpportunityStage::fromString($this->stage)->label();
    }

    public function getStageColorAttribute(): string
    {
        return OpportunityStage::fromString($this->stage)->color();
    }

    protected static function newFactory()
    {
        return OpportunityFactory::new();
    }
}
