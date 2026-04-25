<?php

namespace Modules\POS\Domain\Enums;

enum StockModelType: string
{
    case Order      = 'order';
    case Returned   = 'returned';
    case Damaged    = 'damaged';
    case OfferPrice = 'offer_price';
    case Purchase   = 'purchases';
}
