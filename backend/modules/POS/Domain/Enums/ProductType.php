<?php

namespace Modules\POS\Domain\Enums;

enum ProductType: int
{
    case Regular   = 1;
    case Wholesale = 2;
}
