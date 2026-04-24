<?php

namespace Modules\StaticPages\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\StaticPages\Services\StaticPageService;
use Modules\StaticPages\Models\Language;

class StaticPageApiController extends Controller
{
    protected $staticPageService;

    public function __construct(StaticPageService $staticPageService)
    {
        $this->staticPageService = $staticPageService;
    }

    /**
     * Get all public static pages.
     */
    public function index(Request $request)
    {
        try {
            $languageCode = $request->get('lang', 'en');
            $type = $request->get('type');
            $limit = $request->get('limit', 10);

            // Validate language code
            if (!Language::where('code', $languageCode)->where('is_active', true)->exists()) {
                $languageCode = 'en'; // Fallback to English
            }

            $filters = [
                'status' => 'active',
                'is_public' => true,
            ];

            if ($type) {
                $filters['type'] = $type;
            }

            $pages = $this->staticPageService->getAllPages($filters, $limit);

            // Transform pages for API response
            $transformedPages = $pages->map(function ($page) use ($languageCode) {
                return $this->transformPageForApi($page, $languageCode);
            });

            return response()->json([
                'success' => true,
                'data' => $transformedPages,
                'meta' => [
                    'current_page' => $pages->currentPage(),
                    'last_page' => $pages->lastPage(),
                    'per_page' => $pages->perPage(),
                    'total' => $pages->total(),
                    'language' => $languageCode,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch static pages',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific static page by slug.
     */
    public function show(Request $request, $slug)
    {
        try {
            $languageCode = $request->get('lang', 'en');

            // Validate language code
            if (!Language::where('code', $languageCode)->where('is_active', true)->exists()) {
                $languageCode = 'en'; // Fallback to English
            }

            $page = $this->staticPageService->getPageForApi($slug, $languageCode);

            return response()->json([
                'success' => true,
                'data' => $page,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Page not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch page',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get pages by type.
     */
    public function getByType(Request $request, $type)
    {
        try {
            $languageCode = $request->get('lang', 'en');

            // Validate language code
            if (!Language::where('code', $languageCode)->where('is_active', true)->exists()) {
                $languageCode = 'en'; // Fallback to English
            }

            $pages = $this->staticPageService->getPagesByType($type, $languageCode);

            // Transform pages for API response
            $transformedPages = $pages->map(function ($page) use ($languageCode) {
                return $this->transformPageForApi($page, $languageCode);
            });

            return response()->json([
                'success' => true,
                'data' => $transformedPages,
                'meta' => [
                    'type' => $type,
                    'language' => $languageCode,
                    'count' => $pages->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pages by type',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search static pages.
     */
    public function search(Request $request)
    {
        try {
            $query = $request->get('q');
            $languageCode = $request->get('lang', 'en');
            $limit = $request->get('limit', 10);

            if (!$query) {
                return response()->json([
                    'success' => false,
                    'message' => 'Search query is required',
                ], 400);
            }

            // Validate language code
            if (!Language::where('code', $languageCode)->where('is_active', true)->exists()) {
                $languageCode = 'en'; // Fallback to English
            }

            $pages = $this->staticPageService->searchPages($query, $languageCode, $limit);

            // Transform pages for API response
            $transformedPages = $pages->map(function ($page) use ($languageCode) {
                return $this->transformPageForApi($page, $languageCode);
            });

            return response()->json([
                'success' => true,
                'data' => $transformedPages,
                'meta' => [
                    'query' => $query,
                    'language' => $languageCode,
                    'count' => $pages->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search pages',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available languages.
     */
    public function languages()
    {
        try {
            $languages = Language::where('is_active', true)
                ->ordered()
                ->get()
                ->map(function ($language) {
                    return [
                        'code' => $language->code,
                        'name' => $language->name,
                        'native_name' => $language->native_name,
                        'flag' => $language->flag,
                        'direction' => $language->direction,
                        'is_default' => $language->is_default,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $languages,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch languages',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transform page for API response.
     */
    private function transformPageForApi($page, $languageCode)
    {
        // Get translated attributes
        $translatedAttributes = $page->getTranslatedAttributes($languageCode);

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
            'status' => $page->status,
            'author' => $page->author ? [
                'id' => $page->author->id,
                'name' => $page->author->name,
            ] : null,
            'revision' => $page->revision,
            'order' => $page->order,
            'parent_id' => $page->parent_id,
            'has_children' => $page->has_children,
            'created_at' => $page->created_at,
            'updated_at' => $page->updated_at,
            'content' => $translatedAttributes,
            'seo' => $page->getSeoData(),
            'breadcrumbs' => $page->getBreadcrumbs(),
        ];
    }
}
