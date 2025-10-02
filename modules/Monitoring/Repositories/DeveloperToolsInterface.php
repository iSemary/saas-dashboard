<?php

namespace Modules\Monitoring\Repositories;

interface DeveloperToolsInterface
{
    public function getMigrationStatus();
    public function getDebugInfo();
    public function getCacheStats();
    public function getConfigInfo();
    public function runDiagnostics();
}
