# Static Pages Management System

## Overview

The Static Pages Management System provides a comprehensive solution for managing multi-language static pages in the SaaS dashboard. It includes CRUD functionality, multi-language support, API exposure, and seamless integration with the website.

## Features

### Core Features
- **Multi-language Support**: Full support for multiple languages with translatable content
- **CRUD Operations**: Complete Create, Read, Update, Delete functionality
- **SEO Optimization**: Meta titles, descriptions, and keywords support
- **Page Hierarchy**: Support for parent-child page relationships
- **Status Management**: Draft, Active, and Inactive statuses
- **Public/Private Pages**: Control page visibility
- **Revision Tracking**: Automatic revision incrementing on updates
- **Soft Deletes**: Safe deletion with recovery capability

### API Features
- **RESTful API**: Complete REST API for static pages
- **Language Localization**: API responses in requested language
- **Search Functionality**: Full-text search across pages
- **Filtering**: Filter by type, status, and other criteria
- **Pagination**: Efficient pagination for large datasets

## Database Structure

### Tables

#### `static_pages`
- `id`: Primary key
- `name`: Page name
- `slug`: URL-friendly identifier
- `description`: Page description
- `status`: active, inactive, draft
- `type`: page, policy, about_us, landing_page, blog
- `image`: Banner or thumbnail image
- `meta_title`: SEO title
- `meta_description`: SEO description
- `meta_keywords`: SEO keywords
- `is_public`: Public visibility flag
- `author_id`: User who created the page
- `revision`: Version number
- `order`: Custom sorting order
- `parent_id`: Parent page for hierarchy
- `custom_fields`: JSON for additional data
- `created_at`, `updated_at`, `deleted_at`: Timestamps

#### `static_page_attributes`
- `id`: Primary key
- `static_page_id`: Foreign key to static_pages
- `key`: Attribute key (content, title, subtitle, etc.)
- `value`: Translatable content
- `language_code`: ISO 639-1 language code
- `status`: active, inactive
- `metadata`: Additional metadata
- `created_at`, `updated_at`, `deleted_at`: Timestamps

#### `languages`
- `id`: Primary key
- `name`: Language name
- `code`: ISO 639-1 language code
- `native_name`: Native language name
- `flag`: Flag emoji or image
- `is_active`: Active status
- `is_default`: Default language flag
- `direction`: ltr, rtl
- `date_format`: Date format string
- `time_format`: Time format string
- `currency_code`: Currency code
- `locale_settings`: Additional locale settings
- `sort_order`: Display order
- `custom_fields`: Additional data
- `created_at`, `updated_at`, `deleted_at`: Timestamps

## Models

### StaticPage Model

#### Relationships
- `author()`: Belongs to User
- `parent()`: Belongs to StaticPage (self-referencing)
- `children()`: Has many StaticPage
- `attributes()`: Has many StaticPageAttribute

#### Scopes
- `active()`: Active pages only
- `public()`: Public pages only
- `byType($type)`: Filter by page type
- `bySlug($slug)`: Find by slug
- `rootPages()`: Pages without parent
- `ordered()`: Ordered by order field

#### Methods
- `getAttributeValue($key, $languageCode, $default)`: Get translated attribute
- `setAttributeValue($key, $value, $languageCode, $metadata)`: Set translated attribute
- `getTranslatedAttributes($languageCode)`: Get all attributes for language
- `publish()`: Set status to active
- `unpublish()`: Set status to inactive
- `incrementRevision()`: Increment revision number
- `getSeoData()`: Get SEO metadata
- `getBreadcrumbs()`: Get page breadcrumbs

### StaticPageAttribute Model

#### Relationships
- `staticPage()`: Belongs to StaticPage
- `language()`: Belongs to Language

#### Scopes
- `active()`: Active attributes only
- `byKey($key)`: Filter by attribute key
- `byLanguage($languageCode)`: Filter by language
- `translatable()`: Translatable attributes only

#### Methods
- `updateValue($value, $metadata)`: Update attribute value
- `getTranslationForLanguage($languageCode)`: Get translation for specific language
- `getAllTranslations()`: Get all translations for this attribute
- `hasTranslation($languageCode)`: Check if translation exists

### Language Model

#### Relationships
- `staticPageAttributes()`: Has many StaticPageAttribute

#### Scopes
- `active()`: Active languages only
- `default()`: Default language
- `byCode($code)`: Find by language code
- `ordered()`: Ordered by sort_order

#### Methods
- `setAsDefault()`: Set as default language
- `getLocaleSettings()`: Get locale configuration
- `getFormattedDate($date)`: Format date according to locale
- `getFormattedTime($time)`: Format time according to locale
- `getCompletionPercentage()`: Get translation completion percentage

## Services

### StaticPageService

#### Methods
- `getAllPages($filters, $perPage)`: Get paginated pages with filters
- `getPageById($id)`: Get page by ID
- `getPageBySlug($slug, $languageCode)`: Get page by slug with translations
- `createPage($data)`: Create new page with attributes
- `updatePage($id, $data)`: Update page and attributes
- `deletePage($id)`: Delete page (with validation)
- `publishPage($id)`: Publish page
- `unpublishPage($id)`: Unpublish page
- `getPagesByType($type, $languageCode)`: Get pages by type
- `getPageHierarchy($parentId)`: Get page hierarchy
- `searchPages($query, $languageCode, $limit)`: Search pages
- `getPageStatistics()`: Get page statistics
- `getPageForApi($slug, $languageCode)`: Get page formatted for API

## API Endpoints

### Public API (No Authentication Required)

#### Get All Pages
```
GET /api/static-pages
```

**Parameters:**
- `lang` (optional): Language code (default: en)
- `type` (optional): Page type filter
- `limit` (optional): Number of results (default: 10)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Privacy Policy",
      "slug": "privacy-policy",
      "description": "Our privacy policy",
      "type": "policy",
      "status": "active",
      "is_public": true,
      "content": {
        "content": {
          "value": "<h1>Privacy Policy</h1>...",
          "key": "content",
          "language_code": "en"
        }
      },
      "seo": {
        "title": "Privacy Policy",
        "description": "Our privacy policy",
        "keywords": "privacy, policy"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 10,
    "total": 1,
    "language": "en"
  }
}
```

#### Get Page by Slug
```
GET /api/static-pages/{slug}
```

**Parameters:**
- `lang` (optional): Language code (default: en)

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Privacy Policy",
    "slug": "privacy-policy",
    "content": {
      "content": {
        "value": "<h1>Privacy Policy</h1>...",
        "key": "content",
        "language_code": "en"
      }
    },
    "available_languages": ["en", "ar", "fr"],
    "current_language": "en"
  }
}
```

#### Get Pages by Type
```
GET /api/static-pages/type/{type}
```

**Parameters:**
- `lang` (optional): Language code (default: en)

#### Search Pages
```
GET /api/static-pages/search?q={query}
```

**Parameters:**
- `q`: Search query
- `lang` (optional): Language code (default: en)
- `limit` (optional): Number of results (default: 10)

#### Get Available Languages
```
GET /api/static-pages/languages
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "code": "en",
      "name": "English",
      "native_name": "English",
      "flag": "🇺🇸",
      "direction": "ltr",
      "is_default": true
    }
  ]
}
```

### Admin API (Authentication Required)

#### CRUD Operations
```
GET    /api/admin/static-pages/pages          # List pages
POST   /api/admin/static-pages/pages          # Create page
GET    /api/admin/static-pages/pages/{id}     # Get page
PUT    /api/admin/static-pages/pages/{id}     # Update page
DELETE /api/admin/static-pages/pages/{id}     # Delete page
```

## Usage Examples

### Creating a Multi-language Page

```php
use Modules\StaticPages\Services\StaticPageService;

$service = new StaticPageService();

$pageData = [
    'name' => 'Privacy Policy',
    'slug' => 'privacy-policy',
    'description' => 'Our privacy policy',
    'type' => 'policy',
    'status' => 'active',
    'is_public' => true,
    'meta_title' => 'Privacy Policy',
    'meta_description' => 'Learn about our privacy policy',
    'attributes' => [
        'content' => [
            'en' => '<h1>Privacy Policy</h1><p>English content...</p>',
            'ar' => '<h1>سياسة الخصوصية</h1><p>المحتوى العربي...</p>',
            'fr' => '<h1>Politique de Confidentialité</h1><p>Contenu français...</p>'
        ],
        'title' => [
            'en' => 'Privacy Policy',
            'ar' => 'سياسة الخصوصية',
            'fr' => 'Politique de Confidentialité'
        ]
    ]
];

$page = $service->createPage($pageData);
```

### Getting Translated Content

```php
$page = StaticPage::find(1);

// Get English content
$englishContent = $page->getAttributeValue('content', 'en');

// Get Arabic content
$arabicContent = $page->getAttributeValue('content', 'ar');

// Get all translations
$allTranslations = $page->getAllTranslations();
```

### API Usage in Frontend

```javascript
// Get privacy policy in Arabic
fetch('/api/static-pages/privacy-policy?lang=ar')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      const content = data.data.content.content.value;
      document.getElementById('content').innerHTML = content;
    }
  });

// Search pages
fetch('/api/static-pages/search?q=privacy&lang=en')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      data.data.forEach(page => {
        console.log(page.name, page.slug);
      });
    }
  });
```

## Testing

### Unit Tests

The system includes comprehensive unit tests covering:

- Model creation and relationships
- Attribute management
- Translation functionality
- Scopes and filters
- API responses
- Service methods

Run tests:
```bash
php artisan test modules/StaticPages/tests/Unit/StaticPageTest.php
```

### Test Coverage

- ✅ Model creation and validation
- ✅ Multi-language attribute management
- ✅ Page hierarchy and relationships
- ✅ Status management (publish/unpublish)
- ✅ SEO data generation
- ✅ Breadcrumb generation
- ✅ Soft deletes
- ✅ Scopes and filters
- ✅ API endpoint responses
- ✅ Service layer methods

## Integration with Website

### Next.js Integration

The static pages API can be easily integrated with a Next.js website:

```javascript
// pages/privacy-policy.js
import { useState, useEffect } from 'react';

export default function PrivacyPolicy() {
  const [page, setPage] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const lang = localStorage.getItem('language') || 'en';
    
    fetch(`/api/static-pages/privacy-policy?lang=${lang}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          setPage(data.data);
        }
        setLoading(false);
      });
  }, []);

  if (loading) return <div>Loading...</div>;
  if (!page) return <div>Page not found</div>;

  return (
    <div>
      <h1>{page.name}</h1>
      <div dangerouslySetInnerHTML={{ __html: page.content.content.value }} />
    </div>
  );
}
```

### Language Switching

```javascript
// components/LanguageSwitcher.js
import { useState, useEffect } from 'react';

export default function LanguageSwitcher() {
  const [languages, setLanguages] = useState([]);
  const [currentLang, setCurrentLang] = useState('en');

  useEffect(() => {
    fetch('/api/static-pages/languages')
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          setLanguages(data.data);
        }
      });
  }, []);

  const switchLanguage = (langCode) => {
    localStorage.setItem('language', langCode);
    setCurrentLang(langCode);
    window.location.reload(); // Reload to fetch new language content
  };

  return (
    <div className="language-switcher">
      {languages.map(lang => (
        <button
          key={lang.code}
          onClick={() => switchLanguage(lang.code)}
          className={currentLang === lang.code ? 'active' : ''}
        >
          {lang.flag} {lang.native_name}
        </button>
      ))}
    </div>
  );
}
```

## Best Practices

### Content Management
1. **Always provide fallback content** in the default language
2. **Use consistent attribute keys** across all pages
3. **Validate content length** for SEO purposes
4. **Use semantic HTML** in content attributes
5. **Implement content versioning** for important pages

### API Usage
1. **Always check the success flag** in API responses
2. **Handle language fallbacks** gracefully
3. **Cache API responses** for better performance
4. **Use appropriate HTTP status codes** for errors
5. **Implement rate limiting** for public APIs

### Performance
1. **Index frequently queried fields** (slug, status, type)
2. **Use eager loading** for relationships
3. **Implement caching** for static content
4. **Optimize database queries** with proper scopes
5. **Use pagination** for large datasets

## Troubleshooting

### Common Issues

#### Page Not Found
- Check if page status is 'active'
- Verify page is marked as public
- Ensure slug is correct and unique

#### Translation Missing
- Check if language is active in languages table
- Verify attribute exists for the requested language
- Check if attribute status is 'active'

#### API Errors
- Verify API endpoint URL
- Check required parameters
- Ensure proper authentication for admin endpoints

#### Performance Issues
- Check database indexes
- Review query optimization
- Implement caching strategies

### Debug Mode

Enable debug mode to see detailed error information:

```php
// In .env file
APP_DEBUG=true
LOG_LEVEL=debug
```

## Future Enhancements

### Planned Features
- **Content Versioning**: Track content changes over time
- **Workflow Management**: Approval workflows for content
- **Advanced Search**: Full-text search with filters
- **Content Templates**: Reusable content templates
- **Analytics Integration**: Track page views and engagement
- **CDN Integration**: Serve static content from CDN
- **Content Scheduling**: Schedule content publication
- **Multi-tenant Support**: Tenant-specific content management

### API Improvements
- **GraphQL Support**: GraphQL API for flexible queries
- **Webhook Support**: Real-time content updates
- **Bulk Operations**: Batch create/update operations
- **Content Validation**: Advanced content validation rules
- **Export/Import**: Content backup and migration tools

## Support

For technical support or feature requests, please contact the development team or create an issue in the project repository.

## Changelog

### Version 1.0.0
- Initial release
- Multi-language support
- CRUD operations
- API endpoints
- Unit tests
- Documentation

---

*Last updated: 2025-01-10*
