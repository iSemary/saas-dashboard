<?php

declare(strict_types=1);

namespace Modules\EmailMarketing\Presentation\Http\Controllers\Api;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\Traits\ApiResponseEnvelopeTrait;
use Illuminate\Http\JsonResponse;
use Modules\EmailMarketing\Infrastructure\Persistence\EmCampaignRepositoryInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmContactRepositoryInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmContactListRepositoryInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmTemplateRepositoryInterface;
use Modules\EmailMarketing\Infrastructure\Persistence\EmSendingLogRepositoryInterface;

class EmDashboardApiController extends ApiController
{
    use ApiResponseEnvelopeTrait;

    public function __construct(
        protected EmCampaignRepositoryInterface $campaignRepository,
        protected EmContactRepositoryInterface $contactRepository,
        protected EmContactListRepositoryInterface $contactListRepository,
        protected EmTemplateRepositoryInterface $templateRepository,
        protected EmSendingLogRepositoryInterface $sendingLogRepository,
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
        $unsubscribedContacts = $this->contactRepository->count(['status' => 'unsubscribed']);

        $sentLogs = $this->sendingLogRepository->count(['status' => 'sent']);
        $openedLogs = $this->sendingLogRepository->count(['status' => 'opened']);
        $clickedLogs = $this->sendingLogRepository->count(['status' => 'clicked']);
        $bouncedLogs = $this->sendingLogRepository->count(['status' => 'bounced']);
        $openRate = $sentLogs > 0 ? round(($openedLogs / $sentLogs) * 100, 2) : 0;
        $clickRate = $sentLogs > 0 ? round(($clickedLogs / $sentLogs) * 100, 2) : 0;
        $bounceRate = $sentLogs > 0 ? round(($bouncedLogs / $sentLogs) * 100, 2) : 0;

        return $this->success(data: [
            'total_campaigns' => $totalCampaigns,
            'draft_campaigns' => $draftCampaigns,
            'sent_campaigns' => $sentCampaigns,
            'scheduled_campaigns' => $scheduledCampaigns,
            'total_contacts' => $totalContacts,
            'total_contact_lists' => $totalContactLists,
            'total_templates' => $totalTemplates,
            'unsubscribed_contacts' => $unsubscribedContacts,
            'open_rate' => $openRate,
            'click_rate' => $clickRate,
            'bounce_rate' => $bounceRate,
        ]);
    }

    public function recentCampaigns(): JsonResponse
    {
        $campaigns = $this->campaignRepository->list(['limit' => 10]);

        return $this->success(data: $campaigns);
    }
}
