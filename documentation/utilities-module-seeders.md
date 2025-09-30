# Utilities Module - Entities, Migrations, and Seeders Documentation

This document provides a comprehensive overview of the Utilities module entities, their corresponding database migrations, and available seeders.

## Overview

The Utilities module contains 13 entities that provide common functionality across the application. Each entity has a corresponding migration file and seeder for data population.

## Entity-Migration-Seeder Mapping

| Entity | Migration File | Seeder File | Status |
|--------|----------------|-------------|---------|
| **Category** | `2025_01_04_233322_create_categories_table.php` | `CategorySeeder.php` | ✅ Created |
| **Currency** | `2014_10_12_000000_create_currencies_table.php` | `CurrencySeeder.php` | ✅ Created |
| **Industry** | `2025_01_05_235511_create_industries_table.php` | `IndustrySeeder.php` | ✅ Created |
| **Tag** | `2025_01_05_230050_create_tags_table.php` | `TagSeeder.php` | ✅ Created |
| **Type** | `2025_01_05_235001_create_types_table.php` | `TypeSeeder.php` | ✅ Created |
| **Unit** | `2025_01_14_183856_create_units_table.php` | `UnitSeeder.php` | ✅ Created |
| **Announcement** | `2025_01_05_233650_create_announcements_table.php` | `AnnouncementSeeder.php` | ✅ Created |
| **Module** | `2025_01_13_173607_create_modules_table.php` | `ModuleSeeder.php` | ✅ Created |
| **Release** | `2025_01_13_183725_create_releases_table.php` | `ReleaseSeeder.php` | ✅ Created |
| **StaticPage** | `2025_01_13_183725_create_static_pages_table.php` | `StaticPageSeeder.php` | ✅ Created |
| **StaticPageAttribute** | `2025_01_13_183925_create_static_page_attributes_table.php` | `StaticPageAttributeSeeder.php` | ✅ Created |
| **Entity** | `2025_01_14_183856_create_entities_table.php` | `EntitySeeder.php` | ✅ Created |
| **ModuleEntity** | `2025_01_14_183856_create_module_entities_table.php` | `ModuleEntitySeeder.php` | ✅ Created |

## Entity Details

### 1. Category
- **Purpose**: Hierarchical categorization system
- **Key Features**: Parent-child relationships, icon support, translatable
- **Seeder Data**: 10 parent categories, 8 child categories
- **Icon Handling**: Uses FileHandler trait for file management

### 2. Currency
- **Purpose**: Multi-currency support with exchange rates
- **Key Features**: ISO 4217 codes, exchange rates, symbol positioning
- **Seeder Data**: 20 major world currencies with current exchange rates
- **Special Fields**: `base_currency`, `exchange_rate`, `symbol_position`

### 3. Industry
- **Purpose**: Business industry classification
- **Key Features**: Icon support, priority ordering
- **Seeder Data**: 25 common business industries
- **Icon Handling**: Uses FileHandler trait for file management

### 4. Tag
- **Purpose**: Flexible tagging system with hierarchy
- **Key Features**: Parent-child relationships, icon support
- **Seeder Data**: 8 parent tags, 20 child tags (technology-focused)
- **Icon Handling**: Uses FileHandler trait for file management

### 5. Type
- **Purpose**: Unit type classification for measurement systems
- **Key Features**: Icon support, priority ordering
- **Seeder Data**: 10 measurement types (weight, length, volume, etc.)
- **Icon Handling**: Uses FileHandler trait for file management

### 6. Unit
- **Purpose**: Measurement units with conversion factors
- **Key Features**: Base unit designation, conversion factors, type relationships
- **Seeder Data**: 50+ measurement units across all types
- **Relationships**: Belongs to Type entity

### 7. Announcement
- **Purpose**: System announcements and notifications
- **Key Features**: Start/end dates, rich text content
- **Seeder Data**: 8 sample announcements with various timeframes
- **Content**: Maintenance notices, feature releases, security updates

### 8. Module
- **Purpose**: System module management
- **Key Features**: Module keys, routes, icons, slogans
- **Seeder Data**: 12 system modules (Auth, Customer, Development, etc.)
- **Icon Handling**: Uses FileHandler trait for file management

### 9. Release
- **Purpose**: Version release management
- **Key Features**: Polymorphic relationships, version tracking
- **Seeder Data**: 5 sample releases for different modules
- **Content**: Detailed release notes with features, fixes, and breaking changes

### 10. StaticPage
- **Purpose**: Static content pages (Terms, Privacy, About, etc.)
- **Key Features**: Rich HTML content, SEO-friendly slugs
- **Seeder Data**: 6 essential pages (Terms, Privacy, About, Contact, Help, FAQ)
- **Content**: Comprehensive legal and informational content

### 11. StaticPageAttribute
- **Purpose**: Additional metadata for static pages
- **Key Features**: Key-value pairs, status management
- **Seeder Data**: 20+ attributes across all static pages
- **Content**: Version info, contact details, metadata

### 12. Entity
- **Purpose**: System entity registry
- **Key Features**: Entity path and name tracking
- **Seeder Data**: 42 system entities across all modules
- **Content**: Complete entity registry for the entire system

### 13. ModuleEntity
- **Purpose**: Module-entity relationship mapping
- **Key Features**: Many-to-many relationships
- **Seeder Data**: 42 relationships mapping entities to their modules
- **Content**: Complete module-entity mapping for the entire system

## Icon Handling

Several entities (Category, Industry, Tag, Type, Module) support icons through the FileHandler trait:

- **Storage**: Icons are stored as file IDs in the database
- **Access**: Icons are served through the FileManager module
- **Folders**: Each entity type has its own folder (categories, industries, tags, types, modules)
- **Metadata**: Width, height, and aspect ratio are automatically captured
- **Security**: Public access level with encryption support

## Database Connection

All entities use the `landlord` database connection, indicating they are part of the multi-tenant system's central database.

## Seeder Dependencies

Some seeders have dependencies on others:

1. **UnitSeeder** depends on **TypeSeeder** (units need types to exist first)
2. **ModuleEntitySeeder** depends on **ModuleSeeder** and **EntitySeeder**
3. **StaticPageAttributeSeeder** depends on **StaticPageSeeder**

## Running the Seeders

To run all Utilities module seeders:

```bash
# Run individual seeders
php artisan db:seed --class=Modules\\Utilities\\Database\\Seeders\\CategorySeeder
php artisan db:seed --class=Modules\\Utilities\\Database\\Seeders\\CurrencySeeder
php artisan db:seed --class=Modules\\Utilities\\Database\\Seeders\\IndustrySeeder
# ... and so on

# Or run the main seeder that calls all of them
php artisan db:seed --class=Modules\\Utilities\\Database\\Seeders\\UtilitiesDatabaseSeeder
```

## Data Volume

The seeders create a substantial amount of sample data:

- **Categories**: 18 total (10 parent + 8 child)
- **Currencies**: 20 major world currencies
- **Industries**: 25 business industries
- **Tags**: 28 total (8 parent + 20 child)
- **Types**: 10 measurement types
- **Units**: 50+ measurement units
- **Announcements**: 8 sample announcements
- **Modules**: 12 system modules
- **Releases**: 5 sample releases
- **Static Pages**: 6 essential pages
- **Static Page Attributes**: 20+ attributes
- **Entities**: 42 system entities
- **Module Entities**: 42 relationships

## Maintenance

When adding new entities to the Utilities module:

1. Create the entity class
2. Create the migration file
3. Create the seeder file
4. Update this documentation
5. Add the entity to the EntitySeeder
6. Add the module-entity relationship to ModuleEntitySeeder

## Notes

- All seeders include proper error handling and informative output
- Icons are set to `null` in seeders as they require actual file uploads
- The seeders are designed to be idempotent (safe to run multiple times)
- All timestamps are automatically handled by Laravel
- Soft deletes are supported by all entities
