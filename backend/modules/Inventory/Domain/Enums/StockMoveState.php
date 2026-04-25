<?php

namespace Modules\Inventory\Domain\Enums;

enum StockMoveState: string
{
    case Draft     = 'draft';
    case Confirmed = 'confirmed';
    case Done      = 'done';
    case Cancelled = 'cancel';
}
