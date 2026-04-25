<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Contact;

use Modules\CRM\Domain\Entities\Contact;
use Modules\CRM\Infrastructure\Persistence\ContactRepositoryInterface;

class UpdateContactUseCase
{
    public function __construct(private readonly ContactRepositoryInterface $contacts) {}

    public function execute(int $id, array $data): Contact
    {
        return $this->contacts->update($id, $data);
    }
}
