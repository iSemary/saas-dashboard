# StaticPages Module Documentation

## Overview

The StaticPages module provides comprehensive static page management functionality including page creation, content management, SEO settings, and page publishing. It enables administrators to create and manage static pages such as About Us, Privacy Policy, Terms of Service, and custom landing pages.

## Architecture

### Module Structure

```
StaticPages/
├── Config/              # Module configuration
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Models/              # Page models
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
├── app/                 # Additional application files
├── database/            # Database migrations
└── tests/               # Module tests
```

## Database Schema

### Core Entities

#### Static Pages
- `id` - Primary key
- `title` - Page title
- `slug` - URL slug
- `content` - Page content (HTML)
- `excerpt` - Page excerpt
- `meta_title` - SEO meta title
- `meta_description` - SEO meta description
- `meta_keywords` - SEO meta keywords
- `status` - Page status (draft, published, archived)
- `published_at` - Publication timestamp
- `created_by` - User who created page
- `updated_by` - User who last updated page
- `view_count` - Page view count
- `created_at`, `updated_at` - Timestamps

#### Page Sections
- `id` - Primary key
- `page_id` - Associated page
- `section_name` - Section name
- `section_order` - Display order
- `content` - Section content
- `is_active` - Active status
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Static Pages

**List Pages:** `GET /api/tenant/static-pages`

**Query Parameters:**
- `status` - Filter by status
- `search` - Search by title
- `published_by` - Filter by creator

**Create Page:** `POST /api/tenant/static-pages`
**Get Page:** `GET /api/tenant/static-pages/{id}`
**Update Page:** `PUT /api/tenant/static-pages/{id}`
**Delete Page:** `DELETE /api/tenant/static-pages/{id}`
**Publish Page:** `POST /api/tenant/static-pages/{id}/publish`
**Unpublish Page:** `POST /api/tenant/static-pages/{id}/unpublish`
**Duplicate Page:** `POST /api/tenant/static-pages/{id}/duplicate`

### Page Sections

**List Sections:** `GET /api/tenant/static-pages/{pageId}/sections`
**Create Section:** `POST /api/tenant/static-pages/{pageId}/sections`
**Get Section:** `GET /api/tenant/static-pages/sections/{id}`
**Update Section:** `PUT /api/tenant/static-pages/sections/{id}`
**Delete Section:** `DELETE /api/tenant/static-pages/sections/{id}`
**Reorder Sections:** `POST /api/tenant/static-pages/{pageId}/sections/reorder`

### Public Pages

**Get Page by Slug:** `GET /api/public/pages/{slug}`
**Get Published Pages:** `GET /api/public/pages`

## Services

### StaticPageService
- Page CRUD operations
- Slug generation
- Content validation
- Publishing logic

### PageSectionService
- Section CRUD operations
- Section ordering
- Section-page associations

### SeoService
- SEO metadata management
- Meta tag generation
- Open Graph support

## Repositories

### StaticPageRepository
- Page data access
- Page filtering and searching
- Slug-based queries
- Published page queries

### PageSectionRepository
- Section data access
- Section filtering and searching
- Page-section relationships
- Order-based queries

## Configuration

### Module Configuration

Module configuration in `Config/staticpages.php`:

```php
return [
    'pages' => [
        'auto_slug' => true,
        'slug_separator' => '-',
        'allow_html' => true,
        'max_content_length' => 100000, // characters
    ],
    'seo' => {
        'default_meta_title_length' => 60,
        'default_meta_description_length' => 160,
        'og_tags_enabled' => true,
    },
    'publishing' => [
        'require_approval' => false,
        'auto_publish' => false,
    ],
];
```

## Page Status

- `draft` - Draft page (not visible)
- `published` - Published page (visible)
- `archived` - Archived page (not visible but kept)

## SEO Features

- Custom meta titles
- Meta descriptions
- Meta keywords
- Open Graph tags
- Twitter Card support
- Schema.org markup

## Content Features

- Rich text editor support
- HTML content
- Section-based content
- Media embedding
- Code blocks
- Tables

## Business Rules

- Slugs must be unique
- Published pages are publicly accessible
- Draft pages are only visible to editors
- Auto-generated slugs based on title
- Page sections support ordering
- SEO metadata is optional

## Permissions

StaticPages module permissions follow the pattern: `staticpages.{resource}.{action}`

- `staticpages.pages.view` - View pages
- `staticpages.pages.create` - Create pages
- `staticpages.pages.edit` - Edit pages
- `staticpages.pages.delete` - Delete pages
- `staticpages.pages.publish` - Publish pages
- `staticpages.pages.seo` - Manage SEO settings
- `staticpages.sections.view` - View sections
- `staticpages.sections.create` - Create sections
- `staticpages.sections.edit` - Edit sections
- `staticpages.sections.delete` - Delete sections

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/StaticPages/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Slug generation tests
- Publishing logic tests

## Related Documentation

- [Content Management Guide](../../backend/documentation/staticpages/content.md)
- [SEO Best Practices](../../backend/documentation/staticpages/seo.md)
