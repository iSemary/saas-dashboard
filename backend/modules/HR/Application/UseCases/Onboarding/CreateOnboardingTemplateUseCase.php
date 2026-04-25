<?php

namespace Modules\HR\Application\UseCases\Onboarding;

use Modules\HR\Domain\Entities\OnboardingTemplate;
use Modules\HR\Infrastructure\Persistence\OnboardingTemplateRepositoryInterface;

class CreateOnboardingTemplateUseCase
{
    public function __construct(
        protected OnboardingTemplateRepositoryInterface $repository,
    ) {}

    public function execute(array $data): OnboardingTemplate
    {
        $data['created_by'] = auth()->id();
        return $this->repository->create($data);
    }
}
