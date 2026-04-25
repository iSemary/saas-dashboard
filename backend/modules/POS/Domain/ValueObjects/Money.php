<?php

namespace Modules\POS\Domain\ValueObjects;

final class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency = 'USD',
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException("Money amount cannot be negative: {$amount}");
        }
    }

    public function add(Money $other): self
    {
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        return new self(max(0, $this->amount - $other->amount), $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self(round($this->amount * $factor, 2), $this->currency);
    }

    public function applyDiscountPercent(float $percent): self
    {
        return new self(round($this->amount * (1 - $percent / 100), 2), $this->currency);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    public function toFloat(): float
    {
        return $this->amount;
    }

    public function __toString(): string
    {
        return number_format($this->amount, 2);
    }
}
