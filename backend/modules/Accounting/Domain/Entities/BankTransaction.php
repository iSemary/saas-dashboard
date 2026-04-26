<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Entities\User;

class BankTransaction extends Model
{
    use SoftDeletes;

    protected $table = 'acc_bank_transactions';

    protected $fillable = [
        'bank_account_id',
        'date',
        'type',
        'amount',
        'description',
        'reference',
        'is_reconciled',
        'reconciliation_id',
        'journal_item_id',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'is_reconciled' => 'boolean',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function isDebit(): bool
    {
        return $this->type === 'debit';
    }

    public function isCredit(): bool
    {
        return $this->type === 'credit';
    }

    // ── Relationships ─────────────────────────────────────

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function reconciliation(): BelongsTo
    {
        return $this->belongsTo(Reconciliation::class, 'reconciliation_id');
    }

    public function journalItem(): BelongsTo
    {
        return $this->belongsTo(JournalItem::class, 'journal_item_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeUnreconciled($query)
    {
        return $query->where('is_reconciled', false);
    }
}
