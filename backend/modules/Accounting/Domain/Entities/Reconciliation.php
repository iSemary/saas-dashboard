<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class Reconciliation extends Model
{
    use SoftDeletes;

    protected $table = 'acc_reconciliations';

    protected $fillable = [
        'bank_account_id',
        'statement_date',
        'statement_balance',
        'book_balance',
        'difference',
        'status',
        'description',
        'completed_at',
        'completed_by',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'statement_date' => 'date',
        'statement_balance' => 'decimal:2',
        'book_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'completed_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function isBalanced(): bool
    {
        return bccomp((string) $this->difference, '0', 2) === 0;
    }

    public function complete(): void
    {
        if (!$this->isBalanced()) {
            throw new \RuntimeException('Cannot complete reconciliation: statement and book balances do not match');
        }

        $this->update([
            'status' => 'matched',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        $this->bankTransactions()->update(['is_reconciled' => true]);
    }

    // ── Relationships ─────────────────────────────────────

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'bank_account_id');
    }

    public function bankTransactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class, 'reconciliation_id');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
