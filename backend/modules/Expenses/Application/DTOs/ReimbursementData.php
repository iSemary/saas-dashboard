<?php
declare(strict_types=1);
namespace Modules\Expenses\Application\DTOs;

class ReimbursementData
{
    public function __construct(
        public readonly ?array $data = null,
    ) {}

    public function toArray(): array
    {
        return array_filter($this->data ?? [], fn($v) => !is_null($v));
    }
}
