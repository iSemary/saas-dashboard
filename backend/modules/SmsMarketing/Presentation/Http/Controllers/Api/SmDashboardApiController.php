<?php

declare(strict_types=1);

namespace Modules\SmsMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Modules\SmsMarketing\Infrastructure\Persistence\SmCampaignRepositoryInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmContactRepositoryInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmContactListRepositoryInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmTemplateRepositoryInterface;
use Modules\SmsMarketing\Infrastructure\Persistence\SmSendingLogRepositoryInterface;

class SmDashboardApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected SmCampaignRepositoryInterface $campaignRepository,
        protected SmContactRepositoryInterface $contactRepository,
        protected SmContactListRepositoryInterface $contactListRepository,
        protected SmTemplateRepositoryInterface $templateRepository,
        protected SmSendingLogRepositoryInterface $sendingLogRepository,
    ) { parent::__construct(); }

    public function stats(): JsonResponse
    {
        $totalCampaigns = $this->campaignRepository->count();
        $draftCampaigns = $this->campaignRepository->count(['status' => 'draft']);
        $sentCampaigns = $this->campaignRepository->count(['status' => 'sent']);
        $scheduledCampaigns = $this->campaignRepository->count(['status' => 'scheduled']);
        $totalContacts = $this->contactRepository->count(['status' => 'active']);
        $totalContactLists = $this->contactListRepository->count(['status' => 'active']);
        $totalTemplates = $this->templateRepository->count(['status' => 'active']);
        $optedOutContacts = $this->contactRepository->count(['status' => 'opted_out']);

        $sentLogs = $this->sendingLogRepository->count(['status' => 'sent']);
        $deliveredLogs = $this->sendingLogRepository->count(['status' => 'delivered']);
        $failedLogs = $this->sendingLogRepository->count(['status' => 'failed']);
        $deliveryRate = $sentLogs > 0 ? round(($deliveredLogs / $sentLogs) * 100, 2) : 0;
        $failureRate = $sentLogs > 0 ? round(($failedLogs / $sentLogs) * 100, 2) : 0;
        $totalCost = $this->sendingLogRepository->sum('cost');

        return $this->success(data: [
            'total_campaigns' => $totalCampaigns,
            'draft_campaigns' => $draftCampaigns,
            'sent_campaigns' => $sentCampaigns,
            'scheduled_campaigns' => $scheduledCampaigns,
            'total_contacts' => $totalContacts,
            'total_contact_lists' => $totalContactLists,
            'total_templates' => $totalTemplates,
            'opted_out_contacts' => $optedOutContacts,
            'delivery_rate' => $deliveryRate,
            'failure_rate' => $failureRate,
            'total_cost' => $totalCost,
        ]);
    }

    public function recentCampaigns(): JsonResponse
    {
        $campaigns = $this->campaignRepository->list(['limit' => 10]);

        return $this->success(data: $campaigns);
    }
}
