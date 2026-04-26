<?php

namespace Modules\StaticPages\Services;

use Modules\StaticPages\Models\StaticPage;
use Modules\StaticPages\Models\StaticPageAttribute;
use Modules\StaticPages\Models\Language;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StaticPageService
{
    /**
     * Get all static pages with pagination and filters.
     */
    public function getAllPages($filters = [], $perPage = 15)
    {
        $query = StaticPage::with(['author', 'parent', 'children']);

        // Apply filters
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['is_public'])) {
            $query->where('is_public', $filters['is_public']);
        }

        if (isset($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query->ordered()->paginate($perPage);
    }

    /**
     * Get a static page by ID.
     */
    public function getPageById($id)
    {
        return StaticPage::with(['author', 'parent', 'children', 'attributes'])
            ->findOrFail($id);
    }

    /**
     * Get a static page by slug.
     */
    public function getPageBySlug($slug, $languageCode = 'en')
    {
        $page = StaticPage::with(['author', 'parent', 'children'])
            ->where('slug', $slug)
            ->where('status', 'active')
            ->where('is_public', true)
            ->firstOrFail();

        // Load translated attributes
        $page->load(['attributes' => function ($query) use ($languageCode) {
            $query->where('language_code', $languageCode)
                  ->where('status', 'active');
        }]);

        return $page;
    }

    /**
     * Create a new static page.
     */
    public function createPage($data)
    {
        DB::beginTransaction();
        
        try {
            // Generate slug if not provided
            if (!isset($data['slug'])) {
                $data['slug'] = $this->generateUniqueSlug($data['name']);
            }

            // Set author if not provided
            if (!isset($data['author_id'])) {
                $data['author_id'] = auth()->id();
            }

            $page = StaticPage::create($data);

            // Create default attributes if provided
            if (isset($data['attributes'])) {
                $this->createPageAttributes($page, $data['attributes']);
            }

            DB::commit();
            return $page->load(['author', 'attributes']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update a static page.
     */
    public function updatePage($id, $data)
    {
        DB::beginTransaction();
        
        try {
            $page = StaticPage::findOrFail($id);

            // Generate new slug if name changed
            if (isset($data['name']) && $data['name'] !== $page->name) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
            }

            $page->update($data);

            // Update attributes if provided
            if (isset($data['attributes'])) {
                $this->updatePageAttributes($page, $data['attributes']);
            }

            // Increment revision
            $page->incrementRevision();

            DB::commit();
            return $page->load(['author', 'attributes']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete a static page.
     */
    public function deletePage($id)
    {
        $page = StaticPage::findOrFail($id);
        
        // Check if page has children
        if ($page->children()->count() > 0) {
            throw new \Exception(translate('exception.cannot_delete_with_associated'));
        }

        $page->delete();
        return true;
    }

    /**
     * Publish a static page.
     */
    public function publishPage($id)
    {
        $page = StaticPage::findOrFail($id);
        $page->publish();
        return $page;
    }

    /**
     * Unpublish a static page.
     */
    public function unpublishPage($id)
    {
        $page = StaticPage::findOrFail($id);
        $page->unpublish();
        return $page;
    }

    /**
     * Get pages by type.
     */
    public function getPagesByType($type, $languageCode = 'en')
    {
        return StaticPage::with(['attributes' => function ($query) use ($languageCode) {
            $query->where('language_code', $languageCode)
                  ->where('status', 'active');
        }])
        ->where('type', $type)
        ->where('status', 'active')
        ->where('is_public', true)
        ->ordered()
        ->get();
    }

    /**
     * Get page hierarchy.
     */
    public function getPageHierarchy($parentId = null)
    {
        return StaticPage::with('children')
            ->where('parent_id', $parentId)
            ->where('status', 'active')
            ->ordered()
            ->get();
    }

    /**
     * Search pages.
     */
    public function searchPages($query, $languageCode = 'en', $limit = 10)
    {
        return StaticPage::with(['attributes' => function ($q) use ($languageCode) {
            $q->where('language_code', $languageCode)
              ->where('status', 'active');
        }])
        ->where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('description', 'like', "%{$query}%")
              ->orWhereHas('attributes', function ($attrQuery) use ($query) {
                  $attrQuery->where('value', 'like', "%{$query}%")
                           ->where('status', 'active');
              });
        })
        ->where('status', 'active')
        ->where('is_public', true)
        ->limit($limit)
        ->get();
    }

    /**
     * Get page statistics.
     */
    public function getPageStatistics()
    {
        return [
            'total_pages' => StaticPage::count(),
            'active_pages' => StaticPage::where('status', 'active')->count(),
            'draft_pages' => StaticPage::where('status', 'draft')->count(),
            'public_pages' => StaticPage::where('is_public', true)->count(),
            'pages_by_type' => StaticPage::select('type', DB::raw('COUNT(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type'),
        ];
    }

    /**
     * Create page attributes.
     */
    private function createPageAttributes($page, $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                // Multi-language attributes
                foreach ($value as $langCode => $langValue) {
                    $page->setAttributeValue($key, $langValue, $langCode);
                }
            } else {
                // Single language attribute (default to English)
                $page->setAttributeValue($key, $value, 'en');
            }
        }
    }

    /**
     * Update page attributes.
     */
    private function updatePageAttributes($page, $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                // Multi-language attributes
                foreach ($value as $langCode => $langValue) {
                    $page->setAttributeValue($key, $langValue, $langCode);
                }
            } else {
                // Single language attribute (default to English)
                $page->setAttributeValue($key, $value, 'en');
            }
        }
    }

    /**
     * Generate unique slug.
     */
    private function generateUniqueSlug($name, $excludeId = null)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (StaticPage::where('slug', $slug)
            ->when($excludeId, function ($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get page for API with translations.
     */
    public function getPageForApi($slug, $languageCode = 'en')
    {
        $page = $this->getPageBySlug($slug, $languageCode);
        
        // Get all available translations
        $availableLanguages = $page->attributes()
            ->where('status', 'active')
            ->distinct('language_code')
            ->pluck('language_code');

        // Get translated content
        $translatedContent = $page->getTranslatedAttributes($languageCode);

        return [
            'id' => $page->id,
            'name' => $page->name,
            'slug' => $page->slug,
            'description' => $page->description,
            'type' => $page->type,
            'image' => $page->image,
            'meta_title' => $page->meta_title,
            'meta_description' => $page->meta_description,
            'meta_keywords' => $page->meta_keywords,
            'is_public' => $page->is_public,
            'author' => $page->author ? [
                'id' => $page->author->id,
                'name' => $page->author->name,
            ] : null,
            'revision' => $page->revision,
            'order' => $page->order,
            'parent_id' => $page->parent_id,
            'created_at' => $page->created_at,
            'updated_at' => $page->updated_at,
            'content' => $translatedContent,
            'available_languages' => $availableLanguages,
            'current_language' => $languageCode,
        ];
    }
}
