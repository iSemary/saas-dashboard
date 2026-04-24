<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
use ReflectionClass;
use Illuminate\Database\Eloquent\Model;

class ModelHelper
{
    public static function getAllModels()
    {
        $models = [];
        $modulesPath = base_path('modules');

        // Check if Modules directory exists
        if (!File::exists($modulesPath)) {
            return $models;
        }

        // Get all module directories
        $modules = File::directories($modulesPath);

        foreach ($modules as $module) {
            $entitiesPath = $module . '/Entities';

            if (!File::exists($entitiesPath)) {
                continue;
            }

            $files = File::allFiles($entitiesPath);

            foreach ($files as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                // Get the fully qualified class name
                $class = static::getFullyQualifiedClassName($file);

                if (!$class || !class_exists($class)) {
                    continue;
                }

                try {
                    $reflection = new ReflectionClass($class);

                    // Check if the class is a model and not abstract
                    if (!$reflection->isAbstract() && $reflection->isSubclassOf(Model::class)) {
                        $models[] = $class;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        return array_unique($models);
    }

    private static function getFullyQualifiedClassName($file)
    {
        $contents = file_get_contents($file->getRealPath());

        // Get namespace
        if (preg_match('/namespace\s+(.+?);/', $contents, $matches)) {
            $namespace = $matches[1];
        } else {
            return null;
        }

        // Get class name
        if (preg_match('/class\s+(\w+)/', $contents, $matches)) {
            $className = $matches[1];
        } else {
            return null;
        }

        return $namespace . '\\' . $className;
    }
}
