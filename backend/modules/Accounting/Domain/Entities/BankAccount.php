<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class BankAccount extends Model
{
    use SoftDeletes;

    protected $table = 'acc_bank_accounts';

    protected $fillable = [
        'name',
        'bank_name',
        'account_number',
        'branch_code',
        'swift_code',
        'iban',
        'currency',
        'opening_balance',
        'current_balance',
        'account_id',
        'is_active',
        'description',
        'created_by',
        'custom_fields',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function updateBalance(): void
    {
        $debits  = (float) $this->bankTransactions()->where('type', 'debit')->sum('amount');
        $credits = (float) $this->bankTransactions()->where('type', 'credit')->sum('amount');
        $this->current_balance = (float) $this->opening_balance + $credits - $debits;
        $this->save();
    }

    // ── Relationships ─────────────────────────────────────

    public function chartOfAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bankTransactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class, 'bank_account_id');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
