<?php

declare(strict_types=1);

namespace Modules\Accounting\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Entities\User;

class ChartOfAccount extends Model
{
    use SoftDeletes;

    protected $table = 'acc_chart_of_accounts';

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

    protected $casts = [
        'is_active' => 'boolean',
        'is_leaf' => 'boolean',
        'reconcile' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──────────────────────────────────

    public function isDebitAccount(): bool
    {
        return in_array($this->type, ['asset', 'expense']);
    }

    public function isCreditAccount(): bool
    {
        return in_array($this->type, ['liability', 'equity', 'income']);
    }

    public function updateBalance(): void
    {
        $debitTotal  = (float) $this->journalItems()->sum('debit');
        $creditTotal = (float) $this->journalItems()->sum('credit');

        if ($this->isDebitAccount()) {
            $this->current_balance = (float) $this->opening_balance + $debitTotal - $creditTotal;
        } else {
            $this->current_balance = (float) $this->opening_balance + $creditTotal - $debitTotal;
        }

        $this->save();
    }

    public function canBeDeleted(): bool
    {
        return $this->journalItems()->count() === 0 && $this->children()->count() === 0;
    }

    // ── Relationships ─────────────────────────────────────

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function journalItems(): HasMany
    {
        return $this->hasMany(JournalItem::class, 'account_id');
    }

    // ── Scopes ─────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLeaf($query)
    {
        return $query->where('is_leaf', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // ── Accessors ──────────────────────────────────────────

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
}
