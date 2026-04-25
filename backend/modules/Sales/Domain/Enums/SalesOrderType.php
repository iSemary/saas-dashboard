<?php

namespace Modules\Sales\Domain\Enums;

enum SalesOrderType: string
{
    case Takeaway = 'takeaway';
    case DineIn   = 'dine_in';
    case Delivery = 'delivery';
    case Steward  = 'steward';
}
