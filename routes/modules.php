<?php

use Illuminate\Support\Facades\Route;

// Define the modules directory path
$modulesPath = base_path('modules');

// Ensure the directory exists
if (is_dir($modulesPath)) {
    // Scan the modules directory for subdirectories
    $modules = array_filter(scandir($modulesPath), function ($module) use ($modulesPath) {
        return is_dir($modulesPath . DIRECTORY_SEPARATOR . $module) && $module !== '.' && $module !== '..';
    });

    // Loop through each module and load its routes if the route file exists
    foreach ($modules as $module) {
        $routeFile = $modulesPath . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'web.php';
        if (file_exists($routeFile)) {
            Route::group([], $routeFile);
        }
    }
}
