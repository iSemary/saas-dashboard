<?php

namespace Modules\Subscription\DTOs;

readonly class ProrationResult
{
    public function __construct(
        public float $credit_amount,
        public float $debit_amount,
        public float $net_amount,
        public int $days_remaining,
        public int $days_in_period,
        public float $proration_factor,
        public string $period_start,
        public string $period_end,
        public ?string $description = null,
        public ?array $metadata = null,
    ) {}

    public function isCredit(): bool
    {
        return $this->credit_amount > 0;
    }

    public function isDebit(): bool
    {
        return $this->debit_amount > 0;
    }

    public function toArray(): array
    {
        return [
            'credit_amount' => $this->credit_amount,
            'debit_amount' => $this->debit_amount,
            'net_amount' => $this->net_amount,
            'days_remaining' => $this->days_remaining,
            'days_in_period' => $this->days_in_period,
            'proration_factor' => $this->proration_factor,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'description' => $this->description,
            'metadata' => $this->metadata,
        ];
    }
}
