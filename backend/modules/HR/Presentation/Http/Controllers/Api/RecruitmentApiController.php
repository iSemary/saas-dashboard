<?php

namespace Modules\HR\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\HR\Application\UseCases\Recruitment\ApplyToJobUseCase;
use Modules\HR\Application\UseCases\Recruitment\AdvanceCandidateUseCase;
use Modules\HR\Application\UseCases\Recruitment\ScheduleInterviewUseCase;
use Modules\HR\Application\UseCases\Recruitment\MakeOfferUseCase;
use Modules\HR\Application\UseCases\Recruitment\AcceptOfferUseCase;
use Modules\HR\Presentation\Http\Requests\AdvanceApplicationRequest;
use Modules\HR\Presentation\Http\Requests\ApplyToJobRequest;
use Modules\HR\Presentation\Http\Requests\MakeOfferRequest;
use Modules\HR\Presentation\Http\Requests\ScheduleInterviewRequest;
use Modules\HR\Presentation\Http\Requests\StoreCandidateRequest;
use Modules\HR\Presentation\Http\Requests\StoreJobOpeningRequest;
use Modules\HR\Presentation\Http\Requests\UpdateCandidateRequest;
use Modules\HR\Presentation\Http\Requests\UpdateJobOpeningRequest;
use Modules\HR\Infrastructure\Persistence\JobOpeningRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\CandidateRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\ApplicationRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\PipelineStageRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\InterviewRepositoryInterface;
use Modules\HR\Infrastructure\Persistence\OfferRepositoryInterface;

class RecruitmentApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected JobOpeningRepositoryInterface $jobOpeningRepository,
        protected CandidateRepositoryInterface $candidateRepository,
        protected ApplicationRepositoryInterface $applicationRepository,
        protected PipelineStageRepositoryInterface $pipelineStageRepository,
        protected InterviewRepositoryInterface $interviewRepository,
        protected OfferRepositoryInterface $offerRepository,
        protected ApplyToJobUseCase $applyToJobUseCase,
        protected AdvanceCandidateUseCase $advanceCandidateUseCase,
        protected ScheduleInterviewUseCase $scheduleInterviewUseCase,
        protected MakeOfferUseCase $makeOfferUseCase,
        protected AcceptOfferUseCase $acceptOfferUseCase,
    ) {
        parent::__construct();
    }

    // ─── Job Openings ─────────────────────────────────────────────
    public function indexJobs(Request $request): JsonResponse
    {
        $jobs = $this->jobOpeningRepository->paginate(
            filters: $request->only(['search', 'status', 'department_id']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $jobs);
    }

    public function showJob(int $id): JsonResponse
    {
        $job = $this->jobOpeningRepository->findOrFail($id);
        return $this->success(data: $job);
    }

    public function storeJob(StoreJobOpeningRequest $request): JsonResponse
    {
        $job = $this->jobOpeningRepository->create($request->all());
        return $this->success(data: $job, message: 'Job opening created successfully');
    }

    public function updateJob(UpdateJobOpeningRequest $request, int $id): JsonResponse
    {
        $job = $this->jobOpeningRepository->update($id, $request->all());
        return $this->success(data: $job, message: 'Job opening updated successfully');
    }

    public function destroyJob(int $id): JsonResponse
    {
        $this->jobOpeningRepository->delete($id);
        return $this->success(message: 'Job opening deleted successfully');
    }

    // ─── Candidates ───────────────────────────────────────────────
    public function indexCandidates(Request $request): JsonResponse
    {
        $candidates = $this->candidateRepository->paginate(
            filters: $request->only(['search', 'source', 'blacklisted']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $candidates);
    }

    public function showCandidate(int $id): JsonResponse
    {
        $candidate = $this->candidateRepository->findOrFail($id);
        return $this->success(data: $candidate);
    }

    public function storeCandidate(StoreCandidateRequest $request): JsonResponse
    {
        $candidate = $this->candidateRepository->create($request->all());
        return $this->success(data: $candidate, message: 'Candidate created successfully');
    }

    public function updateCandidate(UpdateCandidateRequest $request, int $id): JsonResponse
    {
        $candidate = $this->candidateRepository->update($id, $request->all());
        return $this->success(data: $candidate, message: 'Candidate updated successfully');
    }

    public function destroyCandidate(int $id): JsonResponse
    {
        $this->candidateRepository->delete($id);
        return $this->success(message: 'Candidate deleted successfully');
    }

    // ─── Applications ─────────────────────────────────────────────
    public function indexApplications(Request $request): JsonResponse
    {
        $applications = $this->applicationRepository->paginate(
            filters: $request->only(['job_opening_id', 'candidate_id', 'status', 'pipeline_stage_id']),
            perPage: $request->input('per_page', 15)
        );
        return $this->success(data: $applications);
    }

    public function showApplication(int $id): JsonResponse
    {
        $application = $this->applicationRepository->findOrFail($id);
        return $this->success(data: $application);
    }

    public function apply(ApplyToJobRequest $request): JsonResponse
    {
        $application = $this->applyToJobUseCase->execute($request->all());
        return $this->success(data: $application, message: 'Application submitted successfully');
    }

    public function advance(int $id, AdvanceApplicationRequest $request): JsonResponse
    {
        $application = $this->advanceCandidateUseCase->execute($id, $request->input('pipeline_stage_id'));
        return $this->success(data: $application, message: 'Application advanced successfully');
    }

    public function reject(int $id, Request $request): JsonResponse
    {
        $application = $this->applicationRepository->findOrFail($id);
        $application->reject(
            reason: $request->input('reason', ''),
            rejectedBy: auth()->id()
        );
        return $this->success(data: $application, message: 'Application rejected');
    }

    // ─── Interviews ──────────────────────────────────────────────
    public function scheduleInterview(int $applicationId, ScheduleInterviewRequest $request): JsonResponse
    {
        $interview = $this->scheduleInterviewUseCase->execute($applicationId, $request->all());
        return $this->success(data: $interview, message: 'Interview scheduled successfully');
    }

    // ─── Offers ───────────────────────────────────────────────────
    public function makeOffer(int $applicationId, MakeOfferRequest $request): JsonResponse
    {
        $offer = $this->makeOfferUseCase->execute($applicationId, $request->all());
        return $this->success(data: $offer, message: 'Offer created successfully');
    }

    public function sendOffer(int $id): JsonResponse
    {
        $offer = $this->offerRepository->findOrFail($id);
        $offer->send(auth()->id());
        return $this->success(data: $offer, message: 'Offer sent successfully');
    }

    public function acceptOffer(int $id): JsonResponse
    {
        $offer = $this->acceptOfferUseCase->execute($id);
        return $this->success(data: $offer, message: 'Offer accepted successfully');
    }

    public function rejectOffer(int $id, Request $request): JsonResponse
    {
        $offer = $this->offerRepository->findOrFail($id);
        $offer->reject($request->input('reason', ''));
        return $this->success(data: $offer, message: 'Offer rejected');
    }

    public function listInterviews(Request $request): JsonResponse
    {
        $interviews = $this->interviewRepository->paginate(
            perPage: $request->input('per_page', 15),
            filters: $request->only(['application_id', 'candidate_id', 'status'])
        );

        return $this->success(data: $interviews);
    }

    public function listOffers(Request $request): JsonResponse
    {
        $offers = $this->offerRepository->paginate(
            perPage: $request->input('per_page', 15),
            filters: $request->only(['application_id', 'candidate_id', 'status'])
        );

        return $this->success(data: $offers);
    }

    // ─── Pipeline Stages ──────────────────────────────────────────
    public function indexPipelineStages(): JsonResponse
    {
        $stages = $this->pipelineStageRepository->getAll();
        return $this->success(data: $stages);
    }

    public function storePipelineStage(Request $request): JsonResponse
    {
        $stage = $this->pipelineStageRepository->create($request->all());
        return $this->success(data: $stage, message: 'Pipeline stage created successfully');
    }

    public function updatePipelineStage(Request $request, int $id): JsonResponse
    {
        $stage = $this->pipelineStageRepository->update($id, $request->all());
        return $this->success(data: $stage, message: 'Pipeline stage updated successfully');
    }

    public function destroyPipelineStage(int $id): JsonResponse
    {
        $this->pipelineStageRepository->delete($id);
        return $this->success(message: 'Pipeline stage deleted successfully');
    }
}
