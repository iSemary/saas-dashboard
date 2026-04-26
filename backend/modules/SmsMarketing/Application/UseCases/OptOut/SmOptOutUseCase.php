<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Application\UseCases\OptOut;

use Modules\SmsMarketing\Infrastructure\Persistence\SmContactRepositoryInterface;
use Modules\SmsMarketing\Application\DTOs\OptOut\CreateSmOptOutDTO;

class SmOptOutUseCase
{
    public function __construct(
        private readonly SmContactRepositoryInterface $contactRepo,
    ) {}

    public function optOut(CreateSmOptOutDTO $dto): void
    {
        $contact = $this->contactRepo->findOrFail($dto->contact_id);
        $contact->update(['status' => 'opted_out']);
    }

    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->contactRepo->paginate(array_merge($filters, ['status' => 'opted_out']), $perPage);
    }
}
