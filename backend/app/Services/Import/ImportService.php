<?php

namespace App\Services\Import;

use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ImportService
{
    protected array $adapters = [];

    public function registerAdapter(string $entity, ImportAdapterInterface $adapter): void
    {
        $this->adapters[$entity] = $adapter;
    }

    public function getAdapter(string $entity): ?ImportAdapterInterface
    {
        return $this->adapters[$entity] ?? null;
    }

    /**
     * Preview import file - validates and returns valid/invalid rows
     */
    public function preview(string $entity, $file): array
    {
        $adapter = $this->getAdapter($entity);
        if (!$adapter) {
            throw new \InvalidArgumentException("No import adapter registered for entity: {$entity}");
        }

        $rows = $this->parseFile($file);
        $headers = array_shift($rows);
        
        $validRows = [];
        $invalidRows = [];
        
        $rowNumber = 2; // Start at 2 (1 is header)
        foreach ($rows as $row) {
            $rowData = array_combine($headers, $row);
            
            // Skip completely empty rows
            if (empty(array_filter($rowData, fn($v) => !empty($v)))) {
                $rowNumber++;
                continue;
            }
            
            $errors = $adapter->validateRow($rowData, $rowNumber);
            
            if (empty($errors)) {
                $validRows[] = [
                    'row' => $rowNumber,
                    'data' => $rowData,
                ];
            } else {
                $invalidRows[] = [
                    'row' => $rowNumber,
                    'data' => $rowData,
                    'errors' => $errors,
                ];
            }
            
            $rowNumber++;
        }

        return [
            'valid_rows' => $validRows,
            'invalid_rows' => $invalidRows,
            'total_rows' => $rowNumber - 2,
            'headers' => $headers,
        ];
    }

    /**
     * Process import of valid rows
     */
    public function import(string $entity, array $validRows, int $userId): ImportResult
    {
        $adapter = $this->getAdapter($entity);
        if (!$adapter) {
            throw new \InvalidArgumentException("No import adapter registered for entity: {$entity}");
        }

        $result = new ImportResult();
        
        foreach ($validRows as $rowData) {
            try {
                $transformedData = $adapter->transformRow($rowData['data']);
                $importedId = $adapter->importRow($transformedData, $userId);
                
                if ($importedId) {
                    $result->successCount++;
                    $result->importedIds[] = $importedId;
                } else {
                    $result->errorCount++;
                    $result->errors[] = "Row {$rowData['row']}: Failed to import";
                }
            } catch (\Exception $e) {
                $result->errorCount++;
                $result->errors[] = "Row {$rowData['row']}: {$e->getMessage()}";
            }
        }

        return $result;
    }

    /**
     * Generate template file for download
     */
    public function generateTemplate(string $entity): string
    {
        $adapter = $this->getAdapter($entity);
        if (!$adapter) {
            throw new \InvalidArgumentException("No import adapter registered for entity: {$entity}");
        }

        $templateData = $adapter->getTemplateData();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Write headers
        $headers = $templateData[0];
        foreach ($headers as $col => $header) {
            $sheet->setCellValueByColumnAndRow($col + 1, 1, $header);
        }
        
        // Write sample data if available
        if (isset($templateData[1])) {
            $sampleRow = $templateData[1];
            foreach ($headers as $col => $header) {
                $sheet->setCellValueByColumnAndRow($col + 1, 2, $sampleRow[$header] ?? '');
            }
        }
        
        // Style headers
        $headerStyle = $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1');
        $headerStyle->getFont()->setBold(true);
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
        $headerStyle->getFill()->getStartColor()->setRGB('E0E0E0');
        
        // Auto-size columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        $filename = storage_path('app/templates/' . $entity . '-import-template.xlsx');
        
        // Ensure directory exists
        if (!is_dir(dirname($filename))) {
            mkdir(dirname($filename), 0755, true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);
        
        return $filename;
    }

    /**
     * Parse Excel or CSV file
     */
    protected function parseFile($file): array
    {
        $extension = $file->getClientOriginalExtension();
        
        if (in_array($extension, ['csv', 'txt'])) {
            return $this->parseCsv($file);
        }
        
        return $this->parseExcel($file);
    }

    /**
     * Parse Excel file
     */
    protected function parseExcel($file): array
    {
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();
        
        return $data;
    }

    /**
     * Parse CSV file
     */
    protected function parseCsv($file): array
    {
        $data = [];
        $handle = fopen($file->getPathname(), 'r');
        
        if ($handle !== false) {
            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $data[] = $row;
            }
            fclose($handle);
        }
        
        return $data;
    }
}
