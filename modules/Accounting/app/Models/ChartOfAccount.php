<?php

namespace Modules\Accounting\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class ChartOfAccount extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'sub_type',
        'parent_id',
        'level',
        'is_active',
        'is_leaf',
        'reconcile',
        'currency',
        'opening_balance',
        'current_balance',
        'created_by',
        'custom_fields',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_leaf' => 'boolean',
        'reconcile' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    /**
     * Get the parent account.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    /**
     * Get the child accounts.
     */
    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    /**
     * Get the user who created this account.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the journal items for this account.
     */
    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItem::class, 'account_id');
    }

    /**
     * Scope for active accounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for leaf accounts (no children).
     */
    public function scopeLeaf($query)
    {
        return $query->where('is_leaf', true);
    }

    /**
     * Scope by account type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the full account path.
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get the account code path.
     */
    public function getCodePathAttribute(): string
    {
        $path = [$this->code];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->code);
            $parent = $parent->parent;
        }

        return implode('.', $path);
    }

    /**
     * Check if account is a debit account.
     */
    public function isDebitAccount(): bool
    {
        return in_array($this->type, ['asset', 'expense']);
    }

    /**
     * Check if account is a credit account.
     */
    public function isCreditAccount(): bool
    {
        return in_array($this->type, ['liability', 'equity', 'income']);
    }

    /**
     * Update account balance.
     */
    public function updateBalance()
    {
        $debitTotal = $this->journalItems()->sum('debit');
        $creditTotal = $this->journalItems()->sum('credit');

        if ($this->isDebitAccount()) {
            $this->current_balance = $this->opening_balance + $debitTotal - $creditTotal;
        } else {
            $this->current_balance = $this->opening_balance + $creditTotal - $debitTotal;
        }

        $this->save();
    }
}
