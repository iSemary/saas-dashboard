<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class DocumentationController extends Controller
{
    protected $documentationPath;

    public function __construct()
    {
        $this->documentationPath = base_path('documentation');
    }

    /**
     * Display the documentation index page
     */
    public function index()
    {
        $files = $this->getMarkdownFiles();
        
        return view('landlord.documentation.index', compact('files'));
    }

    /**
     * Display a specific documentation file
     */
    public function show($file)
    {
        $filePath = $this->documentationPath . '/' . $file;
        
        if (!File::exists($filePath) || !str_ends_with($file, '.md')) {
            abort(404, 'Documentation file not found');
        }

        $content = File::get($filePath);
        $files = $this->getMarkdownFiles();
        
        return view('landlord.documentation.show', compact('content', 'file', 'files'));
    }

    /**
     * AJAX endpoint to fetch markdown file content
     */
    public function getContent(Request $request)
    {
        $file = $request->input('file');
        $filePath = $this->documentationPath . '/' . $file;
        
        if (!File::exists($filePath) || !str_ends_with($file, '.md')) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        $content = File::get($filePath);
        
        return response()->json([
            'success' => true,
            'content' => $content,
            'file' => $file
        ]);
    }

    /**
     * Get list of markdown files in documentation directory
     */
    public function getMarkdownFiles()
    {
        $files = [];
        
        if (File::exists($this->documentationPath)) {
            $allFiles = File::allFiles($this->documentationPath);
            
            foreach ($allFiles as $file) {
                if ($file->getExtension() === 'md') {
                    $relativePath = str_replace($this->documentationPath . '/', '', $file->getPathname());
                    $files[] = [
                        'name' => $file->getFilenameWithoutExtension(),
                        'path' => $relativePath,
                        'display_name' => $this->formatDisplayName($file->getFilenameWithoutExtension()),
                        'directory' => $file->getRelativePath(),
                        'size' => $file->getSize(),
                        'modified' => $file->getMTime()
                    ];
                }
            }
            
            // Sort files by directory and name
            usort($files, function($a, $b) {
                if ($a['directory'] === $b['directory']) {
                    return strcmp($a['name'], $b['name']);
                }
                return strcmp($a['directory'], $b['directory']);
            });
        }
        
        return $files;
    }

    /**
     * Format display name from filename
     */
    private function formatDisplayName($filename)
    {
        // Convert kebab-case and snake_case to Title Case
        $formatted = str_replace(['-', '_'], ' ', $filename);
        return ucwords($formatted);
    }

    /**
     * Get file tree structure for navigation
     */
    public function getFileTree()
    {
        $files = $this->getMarkdownFiles();
        $tree = [];
        
        foreach ($files as $file) {
            if (empty($file['directory'])) {
                $tree['root'][] = $file;
            } else {
                $tree[$file['directory']][] = $file;
            }
        }
        
        return response()->json([
            'success' => true,
            'tree' => $tree
        ]);
    }

    /**
     * Parse markdown content to HTML
     */
    public function parseMarkdown($content)
    {
        // Simple markdown parser for basic formatting
        $content = htmlspecialchars($content);
        
        // Headers
        $content = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $content);
        $content = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $content);
        $content = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $content);
        
        // Bold and italic
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
        
        // Code blocks
        $content = preg_replace('/```([^`]+)```/s', '<pre><code>$1</code></pre>', $content);
        $content = preg_replace('/`([^`]+)`/', '<code>$1</code>', $content);
        
        // Links
        $content = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2">$1</a>', $content);
        
        // Lists
        $content = preg_replace('/^\- (.*$)/m', '<li>$1</li>', $content);
        $content = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $content);
        
        // Line breaks
        $content = nl2br($content);
        
        return $content;
    }
}
