<?php

namespace Modules\POS\Domain\Enums;

enum StockDirection: string
{
    case Increment = 'increment';
    case Decrement = 'decrement';
}
