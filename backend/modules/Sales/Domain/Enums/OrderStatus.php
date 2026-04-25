<?php

namespace Modules\Sales\Domain\Enums;

enum OrderStatus: string
{
    case Pending   = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Returned  = 'returned';
}
