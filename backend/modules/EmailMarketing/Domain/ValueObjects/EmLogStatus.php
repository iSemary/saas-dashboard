<?php

namespace Modules\EmailMarketing\Domain\ValueObjects;

enum EmLogStatus: string
{
    case Queued = 'queued';
    case Sent = 'sent';
    case Delivered = 'delivered';
    case Opened = 'opened';
    case Clicked = 'clicked';
    case Bounced = 'bounced';
    case Failed = 'failed';
    case Unsubscribed = 'unsubscribed';
}
