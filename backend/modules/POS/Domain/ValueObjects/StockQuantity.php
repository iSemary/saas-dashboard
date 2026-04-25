<?php

namespace Modules\POS\Domain\ValueObjects;

final class StockQuantity
{
    public function __construct(public readonly int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException("Stock quantity cannot be negative: {$value}");
        }
    }

    public function add(StockQuantity $other): self
    {
        return new self($this->value + $other->value);
    }

    public function subtract(StockQuantity $other): self
    {
        if ($other->value > $this->value) {
            throw new \DomainException("Insufficient stock: cannot subtract {$other->value} from {$this->value}");
        }
        return new self($this->value - $other->value);
    }

    public function isZero(): bool
    {
        return $this->value === 0;
    }

    public function isLessThan(StockQuantity $other): bool
    {
        return $this->value < $other->value;
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
