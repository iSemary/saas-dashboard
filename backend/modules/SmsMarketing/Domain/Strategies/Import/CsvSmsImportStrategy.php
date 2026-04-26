<?php

namespace Modules\SmsMarketing\Domain\Strategies\Import;

use Modules\SmsMarketing\Domain\Entities\SmContact;
use Modules\SmsMarketing\Domain\Entities\SmImportJob;

class CsvSmsImportStrategy implements SmsImportStrategyInterface
{
    public function import(SmImportJob $job): void
    {
        $job->update(['status' => 'processing']);

        $path = storage_path('app/' . $job->file_path);

        if (! file_exists($path)) {
            $job->update(['status' => 'failed', 'errors' => [['message' => translate('message.resource_not_found')]]]);
            return;
        }

        $handle = fopen($path, 'r');
        fgetcsv($handle);
        $mapping = $job->column_mapping ?? [];

        $total = 0;
        $processed = 0;
        $failed = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            $total++;
            try {
                $data = [];
                foreach ($mapping as $dbField => $csvIndex) {
                    $data[$dbField] = $row[$csvIndex] ?? null;
                }
                $data['contact_list_id'] = $job->contact_list_id;
                SmContact::updateOrCreate(['phone' => $data['phone']], $data);
                $processed++;
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = ['row' => $total, 'message' => $e->getMessage()];
            }
        }

        fclose($handle);

        $job->update([
            'status' => $failed === $total ? 'failed' : 'completed',
            'total_rows' => $total,
            'processed_rows' => $processed,
            'failed_rows' => $failed,
            'errors' => $errors ?: null,
        ]);
    }
}
