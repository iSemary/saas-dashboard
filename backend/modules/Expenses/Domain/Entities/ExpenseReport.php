<?php
declare(strict_types=1);
namespace Modules\Expenses\Domain\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Expenses\Domain\ValueObjects\ReportStatus;

class ExpenseReport extends Model
{
    use SoftDeletes;

    protected $table = 'exp_reports';

    protected $fillable = [
        'title', 'description', 'status', 'total_amount',
        'submitted_at', 'approved_at', 'approved_by',
        'rejected_at', 'rejected_by', 'rejection_reason',
        'reimbursed_at', 'reimbursed_by',
        'created_by', 'custom_fields',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'reimbursed_at' => 'datetime',
        'custom_fields' => 'array',
    ];

    // ── Business Methods ──

    public function canTransitionTo(ReportStatus $to): bool
    {
        return ReportStatus::canTransitionFrom($this->status, $to);
    }

    public function transitionState(ReportStatus $to): void
    {
        if (!$this->canTransitionTo($to)) {
            throw new \RuntimeException("Cannot transition report from '{$this->status}' to '{$to->value}'");
        }

        $this->update(['status' => $to->value]);
    }

    public function submit(): void
    {
        $this->transitionState(ReportStatus::SUBMITTED);
        $this->update(['submitted_at' => now()]);
        $this->expenses()->update(['status' => 'pending']);
        event(new \Modules\Expenses\Domain\Events\ReportSubmitted($this));
    }

    public function approve(int $approverId): void
    {
        $this->transitionState(ReportStatus::APPROVED);
        $this->update(['approved_at' => now(), 'approved_by' => $approverId]);
        $this->expenses()->update(['status' => 'approved', 'approved_by' => $approverId, 'approved_at' => now()]);
        event(new \Modules\Expenses\Domain\Events\ReportApproved($this));
    }

    public function reject(int $rejecterId, string $reason = ''): void
    {
        $this->transitionState(ReportStatus::REJECTED);
        $this->update(['rejected_at' => now(), 'rejected_by' => $rejecterId, 'rejection_reason' => $reason]);
        event(new \Modules\Expenses\Domain\Events\ReportRejected($this));
    }

    public function recalculateTotal(): void
    {
        $this->update(['total_amount' => $this->expenses()->sum('amount')]);
    }

    // ── Relationships ──

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'report_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Entities\User::class, 'created_by');
    }
}
