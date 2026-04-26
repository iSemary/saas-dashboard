<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Application\UseCases\Unsubscribe;

use Modules\EmailMarketing\Infrastructure\Persistence\EmContactRepositoryInterface;
use Modules\EmailMarketing\Application\DTOs\Unsubscribe\CreateEmUnsubscribeDTO;

class EmUnsubscribeUseCase
{
    public function __construct(
        private readonly EmContactRepositoryInterface $contactRepo,
    ) {}

    public function unsubscribe(CreateEmUnsubscribeDTO $dto): void
    {
        $contact = $this->contactRepo->findOrFail($dto->contact_id);
        $contact->update(['status' => 'unsubscribed']);
    }

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->contactRepo->paginate(array_merge($filters, ['status' => 'unsubscribed']), $perPage);
    }
}
