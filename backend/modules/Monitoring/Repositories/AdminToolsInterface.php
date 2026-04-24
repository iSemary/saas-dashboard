<?php

namespace Modules\Monitoring\Repositories;

interface AdminToolsInterface
{
    public function runConsistencyCheck();
    public function getDataIntegrityReport();
    public function getOrphanedRecords();
    public function cleanupTempFiles();
    public function optimizeDatabases();
}
