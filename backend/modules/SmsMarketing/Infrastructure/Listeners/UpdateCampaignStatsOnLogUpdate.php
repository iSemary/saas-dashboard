<?php

namespace Modules\SmsMarketing\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Modules\SmsMarketing\Domain\Events\SmCampaignSent;
use Modules\SmsMarketing\Domain\ValueObjects\SmLogStatus;

class UpdateCampaignStatsOnLogUpdate implements ShouldQueue
{
    public function handle(SmCampaignSent $event): void
    {
        $campaign = $event->campaign;

        $stats = DB::table('sm_sending_logs')
            ->where('campaign_id', $campaign->id)
            ->selectRaw('
                COUNT(*) as total_sent,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as failed,
                COALESCE(SUM(cost), 0) as total_cost
            ', [
                SmLogStatus::Delivered->value,
                SmLogStatus::Failed->value,
            ])
            ->first();

        $campaign->update([
            'total_sent' => $stats->total_sent ?? 0,
            'total_delivered' => $stats->delivered ?? 0,
            'total_failed' => $stats->failed ?? 0,
            'total_cost' => $stats->total_cost ?? 0,
        ]);
    }
}
