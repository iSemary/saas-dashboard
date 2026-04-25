<?php

namespace Modules\POS\Domain\Strategies\Pricing;

use Modules\POS\Domain\ValueObjects\Money;

interface PricingStrategyInterface
{
    public function calculate(float $basePrice, float $quantity, array $context = []): Money;

    public function supports(string $type): bool;
}
