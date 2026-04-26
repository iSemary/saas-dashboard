<?php
/**
 * Script to replace hardcoded messages in controllers with translate() calls.
 * Run: php scripts/replace-hardcoded-messages.php
 * 
 * This script does NOT modify files - it outputs the sed commands to run.
 * Review the output before executing.
 */

$base = dirname(__DIR__);
$modulesDir = $base . '/modules';

// Common replacement patterns for apiSuccess/apiError messages
$patterns = [
    // "Entity created successfully" -> translate('message.created_successfully')
    "/apiSuccess\(([^,]+),\s*'([A-Z][a-z]+(?:\s+[A-Za-z]+)*)\s+created successfully'/" 
        => 'apiSuccess($1, translate(\'message.created_successfully\')',
    
    "/apiSuccess\(([^,]+),\s*'([A-Z][a-z]+(?:\s+[A-Za-z]+)*)\s+updated successfully'/"
        => 'apiSuccess($1, translate(\'message.updated_successfully\')',
    
    "/apiSuccess\(([^,]+),\s*'([A-Z][a-z]+(?:\s+[A-Za-z]+)*)\s+deleted successfully'/"
        => 'apiSuccess($1, translate(\'message.deleted_successfully\')',
    
    "/apiSuccess\(\s*null,\s*'([A-Z][a-z]+(?:\s+[A-Za-z]+)*)\s+deleted successfully'/"
        => 'apiSuccess(null, translate(\'message.deleted_successfully\')',
    
    // "Entity not found" -> translate('message.resource_not_found')
    "/apiError\(\s*'([A-Z][a-z]+(?:\s+[A-Za-z]+)*)\s+not found'/"
        => 'apiError(translate(\'message.resource_not_found\')',
    
    // "Failed to X" -> translate('message.operation_failed')
    "/apiError\(\s*'Failed to\s+[^']+',\s*(\d+)/"
        => 'apiError(translate(\'message.operation_failed\'), $1',
    
    // "Unauthorized" -> translate('auth.unauthorized')
    "/'Unauthorized'/"
        => 'translate(\'auth.unauthorized\')',
    
    // "Validation failed" -> translate('message.validation_failed')
    "/'Validation failed'/"
        => 'translate(\'message.validation_failed\')',
];

echo "# Generated sed commands for replacing hardcoded messages\n";
echo "# Review carefully before executing!\n\n";

foreach ($patterns as $regex => $replacement) {
    echo "# Pattern: $regex\n";
    echo "# Replacement: $replacement\n\n";
}
