<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Accounting\Domain\ValueObjects\BudgetStatus;
use Modules\Accounting\Domain\Events\BudgetCreated;
use Modules\Auth\Entities\User;

class Budget extends Model
{
    use SoftDeletes;

    protected $table = 'acc_budgets';

    protected $fillable = [
        'name',
        'fiscal_year_id',
        'department_id',
        'status',
        'total_amount',
        'description',
        'start_date',
        'end_date',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function transitionStatus(BudgetStatus $newStatus): void
    {
        if (!BudgetStatus::canTransitionFrom($this->status, $newStatus)) {
            throw new \RuntimeException("Cannot transition budget from '{$this->status}' to '{$newStatus->value}'");
        }
        $this->update(['status' => $newStatus->value]);
    }

    public function recalculateTotal(): void
    {
        $this->update([
            'total_amount' => $this->budgetItems()->sum('amount'),
        ]);
    }

    public function getSpentAmount(): float
    {
        $accountIds = $this->budgetItems()->pluck('account_id')->toArray();
        return (float) JournalItem::whereIn('account_id', $accountIds)
            ->whereHas('journalEntry', fn($q) => $q->where('state', 'posted'))
            ->sum('debit');
    }

    public function getRemainingAmount(): float
    {
        return (float) $this->total_amount - $this->getSpentAmount();
    }

    // ── Relationships ─────────────────────────────────────

    public function fiscalYear(): BelongsTo
    {
        return $this->belongsTo(FiscalYear::class, 'fiscal_year_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class, 'budget_id');
    }

    // ── Model Events ──────────────────────────────────────

    protected static function booted(): void
    {
        static::created(function ($model) {
            event(new BudgetCreated($model));
        });
    }
}
