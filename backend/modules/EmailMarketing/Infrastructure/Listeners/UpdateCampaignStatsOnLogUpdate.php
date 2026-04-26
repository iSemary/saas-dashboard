<?php

namespace Modules\EmailMarketing\Infrastructure\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use Modules\EmailMarketing\Domain\Events\EmCampaignSent;
use Modules\EmailMarketing\Domain\ValueObjects\EmLogStatus;

class UpdateCampaignStatsOnLogUpdate implements ShouldQueue
{
    public function handle(EmCampaignSent $event): void
    {
        $campaign = $event->campaign;

        $stats = DB::table('em_sending_logs')
            ->where('campaign_id', $campaign->id)
            ->selectRaw('
                COUNT(*) as total_sent,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as bounced,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as opened,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as clicked
            ', [
                EmLogStatus::Delivered->value,
                EmLogStatus::Bounced->value,
                EmLogStatus::Opened->value,
                EmLogStatus::Clicked->value,
            ])
            ->first();

        $campaign->update([
            'total_sent' => $stats->total_sent ?? 0,
            'total_delivered' => $stats->delivered ?? 0,
            'total_bounced' => $stats->bounced ?? 0,
            'total_opened' => $stats->opened ?? 0,
            'total_clicked' => $stats->clicked ?? 0,
        ]);
    }
}
