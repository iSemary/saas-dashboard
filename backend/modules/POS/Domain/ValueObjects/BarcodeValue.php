<?php

namespace Modules\POS\Domain\ValueObjects;

final class BarcodeValue
{
    public function __construct(public readonly string $value)
    {
        $cleaned = preg_replace('/\s+/', '', $value);
        if (empty($cleaned)) {
            throw new \InvalidArgumentException('Barcode value cannot be empty');
        }
        if (!preg_match('/^[A-Za-z0-9\-_\.]+$/', $cleaned)) {
            throw new \InvalidArgumentException("Invalid barcode format: {$cleaned}");
        }
    }

    public function equals(BarcodeValue $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
