<?php

namespace Modules\Localization\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Localization\Repositories\TranslationInterface;
use Illuminate\Support\Facades\Log;

class GenerateTranslationJsonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $translationRepository = app(TranslationInterface::class);
            $result = $translationRepository->generateJson();
            
            if (!$result['success']) {
                Log::error('Translation JSON generation failed: ' . $result['message']);
            } else {
                Log::info('Translation JSON files generated successfully via queue job');
            }
        } catch (\Exception $e) {
            Log::error('Translation JSON generation job failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
