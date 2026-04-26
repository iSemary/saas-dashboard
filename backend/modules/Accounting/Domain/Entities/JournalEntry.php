<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Accounting\Domain\Entities\JournalItem;
use Modules\Accounting\Domain\Entities\FiscalYear;
use Modules\Accounting\Domain\Events\JournalEntryPosted;
use Modules\Accounting\Domain\Events\JournalEntryCreated;
use Modules\Accounting\Domain\ValueObjects\JournalEntryState;
use Modules\Accounting\Domain\Exceptions\InvalidJournalEntryTransition;
use Modules\Auth\Entities\User;

class JournalEntry extends Model
{
    use SoftDeletes;

    protected $table = 'acc_journal_entries';

    protected $fillable = [
        'entry_number',
        'entry_date',
        'state',
        'reference',
        'description',
        'total_debit',
        'total_credit',
        'currency',
        'fiscal_year_id',
        'created_by',
        'posted_by',
        'posted_at',
        'custom_fields',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'total_debit' => 'decimal:2',
        'total_credit' => 'decimal:2',
        'posted_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function transitionState(JournalEntryState $newState): void
    {
        if (!JournalEntryState::canTransitionFrom($this->state, $newState)) {
            throw new InvalidJournalEntryTransition($this->state, $newState->value);
        }

        $old = $this->state;
        $this->update(['state' => $newState->value]);

        if ($newState === JournalEntryState::POSTED) {
            $this->update([
                'posted_by' => auth()->id(),
                'posted_at' => now(),
            ]);
            event(new JournalEntryPosted($this, $old, $newState->value));
        }
    }

    public function canTransitionTo(JournalEntryState $state): bool
    {
        return JournalEntryState::canTransitionFrom($this->state, $state);
    }

    public function isEditable(): bool
    {
        return $this->state === JournalEntryState::DRAFT->value;
    }

    public function recalculateTotals(): void
    {
        $this->update([
            'total_debit'  => $this->journalItems()->sum('debit'),
            'total_credit' => $this->journalItems()->sum('credit'),
        ]);
    }

    public function isBalanced(): bool
    {
        return bccomp(
            (string) $this->fresh()->total_debit,
            (string) $this->fresh()->total_credit,
            2
        ) === 0;
    }

    // ── Relationships ─────────────────────────────────────

    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItem::class, 'journal_entry_id');
    }

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function poster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    // ── Model Events ──────────────────────────────────────

    protected static function booted(): void
    {
        static::created(function ($model) {
            event(new JournalEntryCreated($model));
        });
    }
}
