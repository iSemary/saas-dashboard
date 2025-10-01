# Brand Management System

## Overview

The Brand Management System provides comprehensive functionality for managing brands within the SaaS platform. Each brand is associated with a tenant and includes features for logo management, translatable content, and full CRUD operations.

## Features

### Core Functionality
- **Brand Creation**: Create new brands with logo, name, slug, and description
- **Brand Management**: Full CRUD operations (Create, Read, Update, Delete)
- **Soft Deletes**: Brands can be soft deleted and restored
- **Logo Upload**: Support for image uploads with validation
- **Slug Generation**: Automatic slug generation from brand name
- **Tenant Association**: Each brand belongs to a specific tenant

### Translatable Content
- **Multi-language Support**: Brand name and description are translatable
- **Language Management**: Support for multiple languages through the localization module
- **Translation Interface**: Easy translation management through the admin interface

### Search and Filtering
- **Search Functionality**: Search brands by name, slug, or description
- **Tenant Filtering**: Filter brands by tenant
- **Creator Filtering**: Filter brands by creator
- **Date Range Filtering**: Filter brands by creation date

## Database Schema

### Brands Table
```sql
CREATE TABLE brands (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    logo VARCHAR(255) NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    tenant_id BIGINT UNSIGNED NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    updated_by BIGINT UNSIGNED NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    INDEX idx_tenant_slug (tenant_id, slug),
    INDEX idx_tenant_name (tenant_id, name),
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);
```

## API Endpoints

### Brand Management
- `GET /landlord/brands` - List all brands with pagination
- `POST /landlord/brands` - Create a new brand
- `GET /landlord/brands/{id}` - Get brand by ID
- `PUT /landlord/brands/{id}` - Update brand
- `DELETE /landlord/brands/{id}` - Delete brand (soft delete)
- `POST /landlord/brands/{id}/restore` - Restore soft deleted brand

### Additional Endpoints
- `GET /landlord/brands/slug/{slug}` - Get brand by slug
- `GET /landlord/brands/tenant/{tenantId}` - Get brands by tenant
- `GET /landlord/brands/search?q={query}` - Search brands
- `GET /landlord/brands/stats` - Get brand statistics

## Usage Examples

### Creating a Brand
```php
use App\Brand;

$brand = Brand::create([
    'name' => 'TechCorp',
    'slug' => 'techcorp',
    'description' => 'Leading technology solutions provider',
    'tenant_id' => 1,
    'created_by' => auth()->id(),
]);
```

### Searching Brands
```php
use App\Brand;

// Search by name, slug, or description
$brands = Brand::search('tech')->get();

// Filter by tenant
$brands = Brand::forTenant(1)->get();
```

### Using the Service Layer
```php
use App\Services\BrandService;

$brandService = app(BrandService::class);

// Create brand with logo upload
$brand = $brandService->create([
    'name' => 'New Brand',
    'description' => 'Brand description',
    'tenant_id' => 1,
    'logo' => $request->file('logo'),
]);

// Generate unique slug
$slug = $brandService->generateUniqueSlug('Brand Name', 1);
```

## Validation Rules

### Brand Form Request
- **logo**: Optional image file (jpeg, png, jpg, gif, svg, max 2MB)
- **name**: Required string, max 255 characters
- **slug**: Optional string, max 255 characters, unique, regex pattern
- **description**: Optional string, max 5000 characters
- **tenant_id**: Required integer, must exist in tenants table

## Permissions

### Required Permissions
- `read.brands` - View brands
- `create.brands` - Create new brands
- `update.brands` - Update existing brands
- `delete.brands` - Delete brands
- `restore.brands` - Restore soft deleted brands

### Role Assignment
By default, only landlord users have access to brand management functionality.

## File Structure

```
app/
├── Brand.php                          # Brand model
├── Http/
│   ├── Controllers/
│   │   └── Landlord/
│   │       └── BrandController.php    # Brand API controller
│   └── Requests/
│       └── BrandFormRequest.php       # Form validation
├── Repositories/
│   ├── Interfaces/
│   │   └── BrandRepositoryInterface.php
│   └── BrandRepository.php
└── Services/
    └── BrandService.php

database/
├── migrations/
│   └── create_brands_table.php
└── seeders/
    └── Landlord/
        ├── BrandSeeder.php
        └── BrandTranslationSeeder.php

resources/
└── views/
    └── landlord/
        └── brands/                    # Brand views (if implemented)
            ├── index.blade.php
            ├── create.blade.php
            ├── edit.blade.php
            └── show.blade.php
```

## Configuration

### Storage Configuration
Brand logos are stored in the `storage/app/public/brands/logos/` directory. Make sure to:

1. Create the storage link:
```bash
php artisan storage:link
```

2. Set proper permissions:
```bash
chmod -R 755 storage/app/public/brands/
```

### Localization Configuration
Brand translations are managed through the localization module. Ensure the module is properly configured and languages are set up.

## Testing

### Unit Tests
Run the brand management tests:
```bash
php artisan test --filter=Brand
```

### Feature Tests
Test the complete brand management workflow:
```bash
php artisan test --filter=BrandManagement
```

## Troubleshooting

### Common Issues

1. **Logo Upload Fails**
   - Check storage permissions
   - Verify file size limits
   - Ensure correct MIME types

2. **Slug Generation Issues**
   - Check for special characters in brand names
   - Verify unique constraint on slug field

3. **Translation Issues**
   - Ensure localization module is properly installed
   - Check language configuration
   - Verify translation keys exist

### Debug Commands
```bash
# Check brand table structure
php artisan tinker
>>> Schema::getColumnListing('brands')

# Test brand creation
>>> Brand::create(['name' => 'Test', 'tenant_id' => 1, 'created_by' => 1])

# Check translations
>>> Translation::where('translation_key', 'like', 'brand_%')->get()
```

## Security Considerations

1. **File Upload Security**
   - Validate file types and sizes
   - Scan uploaded files for malware
   - Store files outside web root when possible

2. **Access Control**
   - Implement proper permission checks
   - Validate tenant ownership
   - Use middleware for authorization

3. **Data Validation**
   - Sanitize user input
   - Validate all form data
   - Use prepared statements for database queries

## Performance Optimization

1. **Database Indexing**
   - Index frequently queried columns
   - Use composite indexes for common query patterns

2. **Caching**
   - Cache brand data for frequently accessed brands
   - Use Redis for session storage

3. **File Storage**
   - Use CDN for logo delivery
   - Implement image optimization
   - Consider cloud storage for scalability

## Future Enhancements

1. **Brand Analytics**
   - Track brand usage statistics
   - Monitor brand performance metrics

2. **Advanced Features**
   - Brand templates
   - Brand collaboration
   - Brand versioning

3. **Integration**
   - API for external systems
   - Webhook support
   - Third-party service integration
