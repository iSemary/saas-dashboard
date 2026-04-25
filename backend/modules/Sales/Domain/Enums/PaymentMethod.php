<?php

namespace Modules\Sales\Domain\Enums;

enum PaymentMethod: string
{
    case Cash        = 'cash';
    case Card        = 'card';
    case Installment = 'installment';
    case Transfer    = 'transfer';
}
