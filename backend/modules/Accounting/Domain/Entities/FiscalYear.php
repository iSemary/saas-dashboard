<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Accounting\Domain\ValueObjects\FiscalYearStatus;
use Modules\Accounting\Domain\Events\FiscalYearCreated;
use Modules\Accounting\Domain\Exceptions\InvalidFiscalYearTransition;
use Modules\Auth\Entities\User;

class FiscalYear extends Model
{
    use SoftDeletes;

    protected $table = 'acc_fiscal_years';

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
        'is_closed',
        'closing_date',
        'description',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'closing_date' => 'date',
        'is_active' => 'boolean',
        'is_closed' => 'boolean',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function transitionStatus(FiscalYearStatus $newStatus): void
    {
        $currentStatus = $this->is_closed ? FiscalYearStatus::CLOSED->value : ($this->is_active ? FiscalYearStatus::OPEN->value : FiscalYearStatus::CLOSED->value);

        if (!FiscalYearStatus::canTransitionFrom($currentStatus, $newStatus)) {
            throw new InvalidFiscalYearTransition($currentStatus, $newStatus->value);
        }

        $this->update([
            'is_active' => $newStatus === FiscalYearStatus::OPEN,
            'is_closed' => in_array($newStatus, [FiscalYearStatus::CLOSED, FiscalYearStatus::LOCKED]),
            'closing_date' => in_array($newStatus, [FiscalYearStatus::CLOSED, FiscalYearStatus::LOCKED]) ? now() : null,
        ]);
    }

    public function isOpen(): bool
    {
        return $this->is_active && !$this->is_closed;
    }

    public function acceptsEntries(): bool
    {
        return $this->isOpen();
    }

    // ── Relationships ─────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'fiscal_year_id');
    }

    public function budgets(): HasMany
    {
        return $this->hasMany(Budget::class, 'fiscal_year_id');
    }

    // ── Model Events ──────────────────────────────────────

    protected static function booted(): void
    {
        static::created(function ($model) {
            event(new FiscalYearCreated($model));
        });
    }
}
