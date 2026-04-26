<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Expenses\Domain\ValueObjects\ExpenseStatus;
use Modules\Expenses\Domain\Exceptions\InvalidExpenseTransition;

class Expense extends Model
{
    use SoftDeletes;

    protected $table = 'exp_expenses';

    protected $fillable = [
        'title', 'description', 'amount', 'currency', 'date',
        'category_id', 'status', 'reference', 'vendor',
        'receipt', 'receipt_date', 'is_billable',
        'project_id', 'department_id', 'report_id',
        'submitted_at', 'approved_at', 'approved_by',
        'rejected_at', 'rejected_by', 'rejection_reason',
        'reimbursed_at', 'reimbursed_by',
        'created_by', 'custom_fields',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'receipt_date' => 'date',
        'is_billable' => 'boolean',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'reimbursed_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──

    public function canTransitionTo(ExpenseStatus $to): bool
    {
        return ExpenseStatus::canTransitionFrom($this->status, $to);
    }

    public function transitionState(ExpenseStatus $to): void
    {
        if (!$this->canTransitionTo($to)) {
            throw new InvalidExpenseTransition($this->status, $to->value);
        }

        $this->update(['status' => $to->value]);
    }

    public function submit(): void
    {
        $this->transitionState(ExpenseStatus::PENDING);
        $this->update(['submitted_at' => now()]);
        event(new \Modules\Expenses\Domain\Events\ExpenseSubmitted($this));
    }

    public function approve(int $approverId): void
    {
        $this->transitionState(ExpenseStatus::APPROVED);
        $this->update([
            'approved_at' => now(),
            'approved_by' => $approverId,
        ]);
        event(new \Modules\Expenses\Domain\Events\ExpenseApproved($this));
    }

    public function reject(int $rejecterId, string $reason = ''): void
    {
        $this->transitionState(ExpenseStatus::REJECTED);
        $this->update([
            'rejected_at' => now(),
            'rejected_by' => $rejecterId,
            'rejection_reason' => $reason,
        ]);
        event(new \Modules\Expenses\Domain\Events\ExpenseRejected($this));
    }

    public function markReimbursed(int $reimburserId): void
    {
        $this->transitionState(ExpenseStatus::REIMBURSED);
        $this->update([
            'reimbursed_at' => now(),
            'reimbursed_by' => $reimburserId,
        ]);
        event(new \Modules\Expenses\Domain\Events\ExpenseReimbursed($this));
    }

    public function cancel(): void
    {
        $this->transitionState(ExpenseStatus::CANCELLED);
    }

    // ── Relationships ──

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(ExpenseReport::class, 'report_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'rejected_by');
    }

    public function reimbursor(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'reimbursed_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ExpenseTag::class, 'exp_expense_tag', 'expense_id', 'tag_id');
    }
}
