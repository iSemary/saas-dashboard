<?php

declare(strict_types=1);

namespace Modules\CRM\Domain\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class EntityAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Model $entity,
        public readonly int $oldUserId,
        public readonly int $newUserId,
        public readonly ?int $assignedBy = null
    ) {}
}
