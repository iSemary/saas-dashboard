<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Define the modules directory path
        $modulesPath = base_path('modules');

        // Ensure the directory exists
        if (is_dir($modulesPath)) {
            // Scan the modules directory for subdirectories
            $modules = array_filter(scandir($modulesPath), function ($module) use ($modulesPath) {
                return is_dir($modulesPath . DIRECTORY_SEPARATOR . $module) && $module !== '.' && $module !== '..';
            });

            // Loop through each module and register its service providers
            foreach ($modules as $module) {
                $providerFile = $modulesPath . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'Providers' . DIRECTORY_SEPARATOR . $module . 'ServiceProvider.php';
                if (file_exists($providerFile)) {
                    $this->app->register("Modules\\{$module}\\Providers\\{$module}ServiceProvider");
                }
            }
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {}
}
