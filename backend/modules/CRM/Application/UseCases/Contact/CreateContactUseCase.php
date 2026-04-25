<?php

declare(strict_types=1);

namespace Modules\CRM\Application\UseCases\Contact;

use Modules\CRM\Domain\Entities\Contact;
use Modules\CRM\Domain\Events\ContactCreated;
use Modules\CRM\Infrastructure\Persistence\ContactRepositoryInterface;

class CreateContactUseCase
{
    public function __construct(private readonly ContactRepositoryInterface $contacts) {}

    public function execute(array $data, int $userId): Contact
    {
        $data['created_by'] = $userId;
        $contact = $this->contacts->create($data);
        event(new ContactCreated($contact, $data));
        return $contact;
    }
}
