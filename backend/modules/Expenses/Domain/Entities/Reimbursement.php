<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reimbursement extends Model
{
    use SoftDeletes;

    protected $table = 'exp_reimbursements';

    protected $fillable = [
        'reference', 'amount', 'currency', 'status',
        'payment_method', 'payment_reference',
        'processed_at', 'processed_by',
        'notes', 'created_by', 'custom_fields',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    public function expenses(): BelongsToMany
    {
        return $this->belongsToMany(Expense::class, 'exp_expense_reimbursement', 'reimbursement_id', 'expense_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'processed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }
}
