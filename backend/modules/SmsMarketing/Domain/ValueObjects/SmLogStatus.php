<?php

namespace Modules\SmsMarketing\Domain\ValueObjects;

enum SmLogStatus: string
{
    case Queued = 'queued';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Failed = 'failed';
}
