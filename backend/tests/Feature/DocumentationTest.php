<?php

namespace Tests\Feature;

use App\Http\Controllers\Landlord\DocumentationController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\File;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

class DocumentationTest extends BaseTestCase
{
    use WithFaker;

    protected $documentationPath;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->documentationPath = base_path('documentation');
        $this->controller = new DocumentationController();
        
        // Create test documentation files
        $this->createTestDocumentationFiles();
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $this->cleanupTestFiles();
        parent::tearDown();
    }

    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    /** @test */
    public function it_can_parse_markdown_content()
    {
        $markdownContent = "# Test Header\n\nThis is **bold** text and *italic* text.\n\n- List item 1\n- List item 2";
        
        $htmlContent = $this->controller->parseMarkdown($markdownContent);

        $this->assertStringContainsString('<h1>Test Header</h1>', $htmlContent);
        $this->assertStringContainsString('<strong>bold</strong>', $htmlContent);
        $this->assertStringContainsString('<em>italic</em>', $htmlContent);
        $this->assertStringContainsString('<li>List item 1</li>', $htmlContent);
    }

    /** @test */
    public function it_handles_code_blocks_in_markdown()
    {
        $markdownContent = "```php\n<?php\necho 'Hello World';\n```";
        
        $htmlContent = $this->controller->parseMarkdown($markdownContent);

        $this->assertStringContainsString('<pre><code>', $htmlContent);
        $this->assertStringContainsString('echo &#039;Hello World&#039;;', $htmlContent);
    }

    /** @test */
    public function it_handles_links_in_markdown()
    {
        $markdownContent = "[Google](https://google.com)";
        
        $htmlContent = $this->controller->parseMarkdown($markdownContent);

        $this->assertStringContainsString('<a href="https://google.com">Google</a>', $htmlContent);
    }

    /** @test */
    public function it_handles_malformed_markdown_gracefully()
    {
        $malformedMarkdown = "### Header without proper spacing\n**Bold text without closing\n*Italic text without closing";
        
        $htmlContent = $this->controller->parseMarkdown($malformedMarkdown);
        
        // Should not throw an exception and should return some HTML
        $this->assertIsString($htmlContent);
        $this->assertNotEmpty($htmlContent);
    }

    /** @test */
    public function it_escapes_html_in_markdown()
    {
        $markdownWithHtml = "<script>alert('xss')</script>\n\n# Safe Header";
        
        $htmlContent = $this->controller->parseMarkdown($markdownWithHtml);
        
        // HTML should be escaped
        $this->assertStringNotContainsString('<script>', $htmlContent);
        $this->assertStringContainsString('&lt;script&gt;', $htmlContent);
    }

    /** @test */
    public function it_can_list_documentation_files()
    {
        $files = $this->controller->getMarkdownFiles();

        $this->assertIsArray($files);
        $this->assertNotEmpty($files);
        
        // Check if our test files are included
        $fileNames = array_column($files, 'name');
        $this->assertContains('test-documentation', $fileNames);
    }

    /** @test */
    public function it_formats_display_names_correctly()
    {
        $files = $this->controller->getMarkdownFiles();
        
        foreach ($files as $file) {
            if ($file['name'] === 'test-documentation') {
                $this->assertEquals('Test Documentation', $file['display_name']);
            }
        }
    }

    /** @test */
    public function it_includes_file_metadata()
    {
        $files = $this->controller->getMarkdownFiles();
        
        foreach ($files as $file) {
            $this->assertArrayHasKey('name', $file);
            $this->assertArrayHasKey('path', $file);
            $this->assertArrayHasKey('display_name', $file);
            $this->assertArrayHasKey('directory', $file);
            $this->assertArrayHasKey('size', $file);
            $this->assertArrayHasKey('modified', $file);
        }
    }

    private function createTestDocumentationFiles()
    {
        // Create test markdown file in root
        File::put($this->documentationPath . '/test-documentation.md', 
            "# Test Documentation\n\nThis is a test documentation file.\n\n## Features\n\n- Feature 1\n- Feature 2"
        );
        
        // Create test markdown file in subdirectory
        $subDir = $this->documentationPath . '/test-subdir';
        File::makeDirectory($subDir, 0755, true);
        File::put($subDir . '/subdir-documentation.md', 
            "# Subdirectory Documentation\n\nThis is documentation in a subdirectory."
        );
        
        // Create a non-markdown file (should be ignored)
        File::put($this->documentationPath . '/test.txt', 'This is not a markdown file.');
    }

    private function cleanupTestFiles()
    {
        $testFiles = [
            $this->documentationPath . '/test-documentation.md',
            $this->documentationPath . '/test-subdir/subdir-documentation.md',
            $this->documentationPath . '/test.txt',
        ];
        
        foreach ($testFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }
        
        // Remove test subdirectory if empty
        $subDir = $this->documentationPath . '/test-subdir';
        if (File::exists($subDir) && File::isEmptyDirectory($subDir)) {
            File::deleteDirectory($subDir);
        }
    }
}