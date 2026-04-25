<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\UseCases\Onboarding\CreateOnboardingTemplateUseCase;
use Modules\HR\Infrastructure\Persistence\OnboardingProcessRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\OnboardingTemplateRepositoryInterface;

class OnboardingApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected OnboardingTemplateRepositoryInterface $templateRepository,
        protected OnboardingProcessRepositoryInterface $processRepository,
        protected CreateOnboardingTemplateUseCase $createTemplateUseCase,
    ) {
        parent::__construct();
    }

    public function templates(Request $request): JsonResponse
    {
        $data = $this->templateRepository->paginate($request->integer('per_page', 15));
        return $this->success(data: $data);
    }

    public function storeTemplate(Request $request): JsonResponse
    {
        $template = $this->createTemplateUseCase->execute(
            $request->only(['name', 'type', 'department_id', 'is_active'])
        );
        return $this->success(data: $template, message: 'Onboarding template created successfully');
    }

    public function updateTemplate(Request $request, int $id): JsonResponse
    {
        $template = $this->templateRepository->update($id, $request->only(['name', 'type', 'department_id', 'is_active']));
        return $this->success(data: $template, message: 'Onboarding template updated successfully');
    }

    public function processes(Request $request): JsonResponse
    {
        $data = $this->processRepository->paginate($request->integer('per_page', 15));
        return $this->success(data: $data);
    }

    public function storeProcess(Request $request): JsonResponse
    {
        $process = $this->processRepository->create(
            $request->only(['employee_id', 'template_id', 'type', 'status', 'started_at', 'completed_at']) + ['created_by' => auth()->id()]
        );
        return $this->success(data: $process, message: 'Onboarding process created successfully');
    }

    public function updateProcess(Request $request, int $id): JsonResponse
    {
        $process = $this->processRepository->update($id, $request->only(['type', 'status', 'started_at', 'completed_at']));
        return $this->success(data: $process, message: 'Onboarding process updated successfully');
    }
}
