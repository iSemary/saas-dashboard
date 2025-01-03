<?php

namespace Modules\Utilities\Http\Controllers;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CodeBuilderController extends ApiController
{
    public function show()
    {
        $breadcrumbs = [
            ['text' => 'Home', 'link' => route('home')],
            ['text' => "Code Builder"],
        ];

        $result = $this->scanStubs();
        $vars = $result['vars'];
        $files = $result['files'];
        return view('landlord.utilities.code-builder.index', compact('breadcrumbs', 'vars', 'files'));
    }

    public function submit(Request $request)
    {
        try {
            // Get stubs and their content
            $result = $this->scanStubs();
            $files = $result['files'];

            // Get all form inputs which correspond to our variables
            $replacements = $request->except('_token');

            // Create new files based on stubs
            foreach ($files as $file) {
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
                    File::makeDirectory($directory, 0755, true);
                }

                // Write the new file
                File::put($newPath, $content);
            }

            return $this->return(200, "Files generated successfully");
        } catch (\Exception $e) {
            return $this->return(500, "Error generating files: " . $e->getMessage());
        }
    }

    private function determineNewPath(string $stubPath, array $replacements): string
    {
        $newPath = $stubPath;

        // Remove .stub extension if it exists
        $newPath = str_replace('.stub', '', $newPath);

        // Replace variables in the path itself
        foreach ($replacements as $key => $value) {
            $newPath = str_replace('$' . $key . '$', $value, $newPath);
        }

        // Determine the base path for new files
        // You might want to adjust this based on your needs
        $basePath = base_path('Modules/' . $replacements['MODULE_NAME']);

        return $basePath . '/' . $newPath;
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
