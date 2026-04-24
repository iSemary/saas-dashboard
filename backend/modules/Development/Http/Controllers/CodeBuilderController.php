<?php

namespace Modules\Development\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CodeBuilderController extends ApiController implements HasMiddleware
{
    protected $modulesFolder = "modules";
    protected $moduleType = "landlord";
    protected $directoryPermissions = 02775; // drwxrwsr-x

    public static function middleware(): array
    {
        return [
            new Middleware('permission:read.code_builder', only: ['index', 'submit']),
        ];
    }

    public function show()
    {
        if (env("APP_ENV") != "local") {
            abort(404);
        }
        $breadcrumbs = [
            ['text' => translate('home'), 'link' => route('home')],
            ['text' => "Code Builder"],
        ];

        $result = $this->scanStubs();
        $vars = $result['vars'];
        $files = $result['files'];
        return view('landlord.developments.code-builder.index', compact('breadcrumbs', 'vars', 'files'));
    }

    public function submit(Request $request)
    {
        if (env("APP_ENV") != "local") {
            abort(404);
        }
        try {
            $this->moduleType = $request->module_type;

            // Get stubs and their content
            $result = $this->scanStubs();
            $files = $result['files'];

            // Get all form inputs which correspond to our variables
            $replacements = $request->except('_token');

            // First check if Modules directory exists and is writable
            $modulesPath = base_path($this->modulesFolder);
            if (!File::exists($modulesPath)) {
                // Try to create Modules directory if it doesn't exist
                if (!File::makeDirectory($modulesPath, 0775, true)) {
                    return $this->return(500, "Cannot create Modules directory. Please check permissions on: " . $modulesPath);
                }
            }

            if (!is_writable($modulesPath)) {
                return $this->return(500, "Modules directory is not writable. Please check permissions on: " . $modulesPath);
            }

            // Check/Create module directory
            $moduleDir = $modulesPath . '/' . $replacements['MODULE_NAME'];
            if (!File::exists($moduleDir)) {
                if (!File::makeDirectory($moduleDir, 0775, true)) {
                    return $this->return(500, "Cannot create module directory. Please check permissions on: " . $moduleDir);
                }
            }

            $generatedFiles = [];
            // Create new files based on stubs
            foreach ($files as $file) {
                try {
                    // Get the content and replace all variables
                    $content = $file['content'];

                    foreach ($replacements as $key => $value) {
                        $content = str_replace('$' . $key . '$', $value, $content);
                    }

                    // Determine the new file path
                    $newPath = $this->determineNewPath($file['path'], $replacements);

                    // Create directories if they don't exist
                    $directory = dirname($newPath);
                    if (!File::isDirectory($directory)) {
                        if (!File::makeDirectory($directory, 02775, true)) {
                            throw new \Exception("Failed to create directory: " . $directory);
                        }
                    }

                    // Check if directory is writable
                    if (!is_writable($directory)) {
                        throw new \Exception("Directory not writable: " . $directory);
                    }

                    // Write the new file
                    if (File::put($newPath, $content) !== false) {
                        // Set the permissions to rw-rw-r-- (664)
                        chmod($newPath, 0664);

                        $generatedFiles[] = $newPath;
                    } else {
                        throw new \Exception("Failed to write file: " . $newPath);
                    }
                } catch (\Exception $e) {
                    // Clean up any files we created if there's an error
                    foreach ($generatedFiles as $generatedFile) {
                        if (File::exists($generatedFile)) {
                            File::delete($generatedFile);
                        }
                    }
                    throw $e;
                }
            }

            return $this->return(200, "Files generated successfully", [
                'files' => $generatedFiles
            ]);
        } catch (\Exception $e) {
            return $this->return(500, "Error generating files: " . $e->getMessage(), []);
        }
    }


    private function determineNewPath(string $stubPath, array $replacements): string
    {
        $newPath = $stubPath;

        // Remove .stub extension if it exists
        $newPath = str_replace('.stub', '', $newPath);

        // Get the base filename without path
        $filename = basename($newPath);

        // Generate the new filename based on the type
        $newFilename = $this->generateFileName($filename, $replacements);

        // Replace the old filename with the new one in the path
        $newPath = str_replace($filename, $newFilename, $newPath);

        // Replace remaining variables in the path
        foreach ($replacements as $key => $value) {
            $newPath = str_replace('$' . $key . '$', $value, $newPath);
        }

        if (str_contains($filename, 'controller')) {
            $basePath = base_path($this->modulesFolder . '/' . $replacements['MODULE_NAME'] . '/' . '/Http/Controllers/');
        } elseif (str_contains($filename, 'model')) {
            $basePath = base_path($this->modulesFolder . '/' . $replacements['MODULE_NAME'] . '/' . 'Entities/');
        } elseif (str_contains($filename, 'migration')) {
            $basePath = base_path($this->modulesFolder . '/' . $replacements['MODULE_NAME'] . '/' . 'Database/migrations/' . $this->moduleType);
        } elseif (str_contains($filename, 'repository')) {
            $basePath = base_path($this->modulesFolder . '/' . $replacements['MODULE_NAME'] . '/' . 'Repositories/');
        } elseif (str_contains($filename, 'interface')) {
            $basePath = base_path($this->modulesFolder . '/' . $replacements['MODULE_NAME'] . '/' . 'Repositories/');
        } elseif (str_contains($filename, 'service')) {
            $basePath = base_path($this->modulesFolder . '/' . $replacements['MODULE_NAME'] . '/' . 'Services/');
        } elseif (str_contains($filename, 'index.blade') || str_contains($filename, 'editor.blade')) {
            $basePath = base_path('resources/views/' . $this->moduleType . '/' . $replacements['MODULE_PLURAL_TITLE'] . '/' . $replacements['PLURAL_TITLE']);
        } elseif (str_contains($filename, 'index.js')) {
            $basePath = base_path('public/assets/' . $this->moduleType . '/js/' . $replacements['MODULE_PLURAL_TITLE'] . '/' . $replacements['PLURAL_TITLE']);
        } else {
            $basePath = base_path($this->modulesFolder . '/' . $replacements['MODULE_NAME'] . '/' . $replacements['MODULE_NAME']);
        }

        return $basePath . '/' . $newPath;
    }

    private function generateFileName(string $filename, array $replacements): string
    {
        // Extract the base name without extension
        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'php';

        // Special handling for blade files
        if (str_contains($filename, '.blade')) {
            // Extract the view type (index, editor, show, etc.)
            $viewType = str_replace('.blade', '', $baseName);
            return strtolower($viewType) . '.blade.php';
        }

        // Convert to lowercase for consistent matching
        $type = strtolower($baseName);

        switch ($type) {
            case 'controller':
                return $replacements['MODEL_NAME'] . 'Controller.' . $extension;

            case 'model':
                return $replacements['MODEL_NAME'] . '.' . $extension;

            case 'repository':
                return $replacements['MODEL_NAME'] . 'Repository.' . $extension;

            case 'interface':
                return $replacements['MODEL_NAME'] . 'Interface.' . $extension;

            case 'service':
                return $replacements['MODEL_NAME'] . 'Service.' . $extension;

            case 'request':
                return $replacements['MODEL_NAME'] . 'Request.' . $extension;

            case 'resource':
                return $replacements['MODEL_NAME'] . 'Resource.' . $extension;

            case 'migration':
                $timestamp = date('Y_m_d_His');
                return $timestamp . '_create_' . strtolower($replacements['TABLE_NAME']) . '_table.' . $extension;

            case 'seeder':
                return $replacements['MODEL_NAME'] . 'Seeder.' . $extension;

            case 'factory':
                return $replacements['MODEL_NAME'] . 'Factory.' . $extension;

            case 'test':
                return $replacements['MODEL_NAME'] . 'Test.' . $extension;

            default:
                return $filename;
        }
    }

    private function scanStubs(): array
    {
        $variables = [];
        $files = [];
        $stubsPath = base_path('stubs/module-stubs');

        // Check if directory exists
        if (!File::isDirectory($stubsPath)) {
            return [
                'vars' => $variables,
                'files' => $files
            ];
        }

        // Get all files recursively
        $stubFiles = File::allFiles($stubsPath);

        foreach ($stubFiles as $file) {
            $content = File::get($file->getPathname());

            // Store file information
            $files[] = [
                'name' => $file->getFilename(),
                'path' => $file->getRelativePathname(),
                'content' => $content
            ];

            // Updated regex to match only standalone $VARIABLE$ patterns
            // Ensures it doesn't match PHP variables like $this->something
            preg_match_all('/(?<!\$)\$([A-Z][A-Z0-9_]*)\$(?!\$)/', $content, $matches);

            if (isset($matches[1]) && !empty($matches[1])) {
                foreach ($matches[1] as $match) {
                    // Add to variables array if not already present
                    if (!in_array($match, $variables)) {
                        $variables[] = $match;
                    }
                }
            }
        }

        // Sort variables alphabetically
        sort($variables);

        return [
            'vars' => $variables,
            'files' => $files
        ];
    }
}
