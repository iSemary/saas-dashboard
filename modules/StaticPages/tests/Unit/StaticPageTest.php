<?php

namespace Modules\StaticPages\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\StaticPages\Models\StaticPage;
use Modules\StaticPages\Models\StaticPageAttribute;
use Modules\StaticPages\Models\Language;
use Modules\Auth\Entities\User;

class StaticPageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $language;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create();
        
        // Create test language
        $this->language = Language::create([
            'name' => 'English',
            'code' => 'en',
            'native_name' => 'English',
            'flag' => '🇺🇸',
            'is_active' => true,
            'is_default' => true,
            'direction' => 'ltr',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i:s',
            'currency_code' => 'USD',
            'sort_order' => 1,
        ]);
    }

    /** @test */
    public function it_can_create_a_static_page()
    {
        $pageData = [
            'name' => 'Test Page',
            'slug' => 'test-page',
            'description' => 'Test description',
            'type' => 'page',
            'status' => 'active',
            'is_public' => true,
            'author_id' => $this->user->id,
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test meta description',
            'meta_keywords' => 'test, page',
            'order' => 1,
        ];

        $page = StaticPage::create($pageData);

        $this->assertInstanceOf(StaticPage::class, $page);
        $this->assertEquals('Test Page', $page->name);
        $this->assertEquals('test-page', $page->slug);
        $this->assertEquals('active', $page->status);
        $this->assertTrue($page->is_public);
    }

    /** @test */
    public function it_can_set_and_get_attribute_values()
    {
        $page = StaticPage::create([
            'name' => 'Test Page',
            'slug' => 'test-page',
            'author_id' => $this->user->id,
        ]);

        // Set attribute value
        $page->setAttributeValue('content', 'Test content', 'en');
        $page->setAttributeValue('title', 'Test Title', 'en');

        // Get attribute value
        $content = $page->getAttributeValue('content', 'en');
        $title = $page->getAttributeValue('title', 'en');

        $this->assertEquals('Test content', $content);
        $this->assertEquals('Test Title', $title);
    }

    /** @test */
    public function it_can_get_translated_attributes()
    {
        $page = StaticPage::create([
            'name' => 'Test Page',
            'slug' => 'test-page',
            'author_id' => $this->user->id,
        ]);

        // Set attributes for multiple languages
        $page->setAttributeValue('content', 'English content', 'en');
        $page->setAttributeValue('content', 'Arabic content', 'ar');
        $page->setAttributeValue('title', 'English title', 'en');
        $page->setAttributeValue('title', 'Arabic title', 'ar');

        // Get translated attributes
        $enAttributes = $page->getTranslatedAttributes('en');
        $arAttributes = $page->getTranslatedAttributes('ar');

        $this->assertCount(2, $enAttributes);
        $this->assertCount(2, $arAttributes);
        $this->assertEquals('English content', $enAttributes['content']->value);
        $this->assertEquals('Arabic content', $arAttributes['content']->value);
    }

    /** @test */
    public function it_can_publish_and_unpublish_pages()
    {
        $page = StaticPage::create([
            'name' => 'Test Page',
            'slug' => 'test-page',
            'status' => 'draft',
            'author_id' => $this->user->id,
        ]);

        $this->assertEquals('draft', $page->status);

        // Publish page
        $page->publish();
        $this->assertEquals('active', $page->status);

        // Unpublish page
        $page->unpublish();
        $this->assertEquals('inactive', $page->status);
    }

    /** @test */
    public function it_can_increment_revision()
    {
        $page = StaticPage::create([
            'name' => 'Test Page',
            'slug' => 'test-page',
            'author_id' => $this->user->id,
        ]);

        $this->assertEquals(1, $page->revision);

        $page->incrementRevision();
        $this->assertEquals(2, $page->revision);
    }

    /** @test */
    public function it_can_get_seo_data()
    {
        $page = StaticPage::create([
            'name' => 'Test Page',
            'slug' => 'test-page',
            'description' => 'Test description',
            'meta_title' => 'Test Meta Title',
            'meta_description' => 'Test meta description',
            'meta_keywords' => 'test, page',
            'image' => 'test-image.jpg',
            'author_id' => $this->user->id,
        ]);

        $seoData = $page->getSeoData();

        $this->assertEquals('Test Meta Title', $seoData['title']);
        $this->assertEquals('Test meta description', $seoData['description']);
        $this->assertEquals('test, page', $seoData['keywords']);
        $this->assertEquals('test-image.jpg', $seoData['image']);
    }

    /** @test */
    public function it_can_get_breadcrumbs()
    {
        $parentPage = StaticPage::create([
            'name' => 'Parent Page',
            'slug' => 'parent-page',
            'author_id' => $this->user->id,
        ]);

        $childPage = StaticPage::create([
            'name' => 'Child Page',
            'slug' => 'child-page',
            'parent_id' => $parentPage->id,
            'author_id' => $this->user->id,
        ]);

        $breadcrumbs = $childPage->getBreadcrumbs();

        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals('Parent Page', $breadcrumbs[0]['name']);
        $this->assertEquals('Child Page', $breadcrumbs[1]['name']);
    }

    /** @test */
    public function it_can_scope_active_pages()
    {
        StaticPage::create([
            'name' => 'Active Page',
            'slug' => 'active-page',
            'status' => 'active',
            'author_id' => $this->user->id,
        ]);

        StaticPage::create([
            'name' => 'Draft Page',
            'slug' => 'draft-page',
            'status' => 'draft',
            'author_id' => $this->user->id,
        ]);

        $activePages = StaticPage::active()->get();

        $this->assertCount(1, $activePages);
        $this->assertEquals('Active Page', $activePages->first()->name);
    }

    /** @test */
    public function it_can_scope_public_pages()
    {
        StaticPage::create([
            'name' => 'Public Page',
            'slug' => 'public-page',
            'is_public' => true,
            'author_id' => $this->user->id,
        ]);

        StaticPage::create([
            'name' => 'Private Page',
            'slug' => 'private-page',
            'is_public' => false,
            'author_id' => $this->user->id,
        ]);

        $publicPages = StaticPage::public()->get();

        $this->assertCount(1, $publicPages);
        $this->assertEquals('Public Page', $publicPages->first()->name);
    }

    /** @test */
    public function it_can_scope_pages_by_type()
    {
        StaticPage::create([
            'name' => 'Policy Page',
            'slug' => 'policy-page',
            'type' => 'policy',
            'author_id' => $this->user->id,
        ]);

        StaticPage::create([
            'name' => 'About Page',
            'slug' => 'about-page',
            'type' => 'about_us',
            'author_id' => $this->user->id,
        ]);

        $policyPages = StaticPage::byType('policy')->get();

        $this->assertCount(1, $policyPages);
        $this->assertEquals('Policy Page', $policyPages->first()->name);
    }

    /** @test */
    public function it_can_scope_pages_by_slug()
    {
        StaticPage::create([
            'name' => 'Test Page',
            'slug' => 'test-page',
            'author_id' => $this->user->id,
        ]);

        $page = StaticPage::bySlug('test-page')->first();

        $this->assertInstanceOf(StaticPage::class, $page);
        $this->assertEquals('Test Page', $page->name);
    }

    /** @test */
    public function it_can_get_full_path()
    {
        $parentPage = StaticPage::create([
            'name' => 'Parent Page',
            'slug' => 'parent-page',
            'author_id' => $this->user->id,
        ]);

        $childPage = StaticPage::create([
            'name' => 'Child Page',
            'slug' => 'child-page',
            'parent_id' => $parentPage->id,
            'author_id' => $this->user->id,
        ]);

        $this->assertEquals('parent-page', $parentPage->full_path);
        $this->assertEquals('parent-page/child-page', $childPage->full_path);
    }

    /** @test */
    public function it_can_soft_delete_pages()
    {
        $page = StaticPage::create([
            'name' => 'Test Page',
            'slug' => 'test-page',
            'author_id' => $this->user->id,
        ]);

        $page->delete();

        $this->assertSoftDeleted('static_pages', ['id' => $page->id]);
    }

    /** @test */
    public function it_can_have_children()
    {
        $parentPage = StaticPage::create([
            'name' => 'Parent Page',
            'slug' => 'parent-page',
            'author_id' => $this->user->id,
        ]);

        $childPage = StaticPage::create([
            'name' => 'Child Page',
            'slug' => 'child-page',
            'parent_id' => $parentPage->id,
            'author_id' => $this->user->id,
        ]);

        $this->assertTrue($parentPage->has_children);
        $this->assertFalse($childPage->has_children);
        $this->assertCount(1, $parentPage->children);
    }
}
