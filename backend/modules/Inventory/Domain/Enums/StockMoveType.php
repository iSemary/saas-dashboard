<?php

namespace Modules\Inventory\Domain\Enums;

enum StockMoveType: string
{
    case In       = 'in';
    case Out      = 'out';
    case Transfer = 'transfer';
    case Adjust   = 'adjust';
}
