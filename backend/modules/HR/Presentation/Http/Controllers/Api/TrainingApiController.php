<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\UseCases\Training\EnrollEmployeeUseCase;
use Modules\HR\Infrastructure\Persistence\CertificationRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\CourseEnrollmentRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\CourseRepositoryInterface;

class TrainingApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected CourseRepositoryInterface $courseRepository,
        protected CourseEnrollmentRepositoryInterface $enrollmentRepository,
        protected CertificationRepositoryInterface $certificationRepository,
        protected EnrollEmployeeUseCase $enrollEmployeeUseCase,
    ) {
        parent::__construct();
    }

    public function courses(Request $request): JsonResponse
    {
        return $this->success(data: $this->courseRepository->paginate($request->integer('per_page', 15)));
    }

    public function storeCourse(Request $request): JsonResponse
    {
        $course = $this->courseRepository->create($request->only(['title', 'description', 'instructor', 'duration_hours', 'content_url', 'status']) + ['created_by' => auth()->id()]);
        return $this->success(data: $course, message: translate('message.action_completed'));
    }

    public function enrollments(Request $request): JsonResponse
    {
        return $this->success(data: $this->enrollmentRepository->paginate($request->integer('per_page', 15)));
    }

    public function storeEnrollment(Request $request): JsonResponse
    {
        $enrollment = $this->enrollEmployeeUseCase->execute($request->only(['course_id', 'employee_id', 'status', 'score']));
        return $this->success(data: $enrollment, message: translate('message.action_completed'));
    }

    public function certifications(Request $request): JsonResponse
    {
        return $this->success(data: $this->certificationRepository->paginate($request->integer('per_page', 15)));
    }
}
