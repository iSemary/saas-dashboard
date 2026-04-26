<?php

declare(strict_types=1);

namespace Modules\CRM\Infrastructure\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Modules\CRM\Domain\Entities\CrmImportJob;
use Modules\CRM\Domain\Strategies\Import\LeadImportStrategy;
use Modules\CRM\Domain\Strategies\Import\ContactImportStrategy;

class ProcessImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly CrmImportJob $importJob) {}

    public function handle(): void
    {
        $this->importJob->markProcessing();
        
        try {
            $filePath = Storage::disk('private')->path($this->importJob->file_path);
            $handle = fopen($filePath, 'r');
            
            if (!$handle) {
                throw new \RuntimeException(translate('message.operation_failed'));
            }
            
            $headers = fgetcsv($handle);
            $totalRows = 0;
            $processedRows = 0;
            $failedRows = 0;
            $errors = [];
            
            $strategy = match($this->importJob->entity_type) {
                'leads' => app(LeadImportStrategy::class),
                'contacts' => app(ContactImportStrategy::class),
                default => throw new \InvalidArgumentException(translate('message.validation_failed')),
            };
            
            while (($row = fgetcsv($handle)) !== false) {
                $totalRows++;
                $data = array_combine($headers, $row);
                
                try {
                    $mappedData = $this->applyMapping($data, $this->importJob->mapping);
                    $strategy->process($mappedData, $this->importJob->created_by);
                    $processedRows++;
                } catch (\Throwable $e) {
                    $failedRows++;
                    $errors[] = ['row' => $totalRows, 'error' => $e->getMessage()];
                }
                
                // Update progress every 10 rows
                if ($totalRows % 10 === 0) {
                    $this->importJob->updateProgress($totalRows, $processedRows, $failedRows);
                }
            }
            
            fclose($handle);
            
            $this->importJob->markCompleted($totalRows, $processedRows, $failedRows, $errors);
            
        } catch (\Throwable $e) {
            $this->importJob->markFailed($e->getMessage());
        }
    }

    private function applyMapping(array $data, array $mapping): array
    {
        $result = [];
        foreach ($mapping as $field => $csvColumn) {
            if (isset($data[$csvColumn])) {
                $result[$field] = $data[$csvColumn];
            }
        }
        return $result;
    }
}
