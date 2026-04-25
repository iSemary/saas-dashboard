# Localization Module Documentation

## Overview

The Localization module provides comprehensive internationalization (i18n) and localization (l10n) functionality including language management, translations, timezone handling, and locale-specific formatting. It enables the platform to support multiple languages and regions with automatic translation and formatting.

## Architecture

### Module Structure

```
Localization/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # Localization entities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Jobs/                # Background jobs
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
├── Traits/              # Reusable traits
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Languages
- `id` - Primary key
- `name` - Language name
- `code` - ISO 639-1 language code
- `native_name` - Native language name
- `direction` - Text direction (ltr, rtl)
- `flag` - Flag emoji or URL
- `is_default` - Default language flag
- `is_active` - Active language flag
- `sort_order` - Display order
- `created_at`, `updated_at` - Timestamps

#### Translations
- `id` - Primary key
- `language_id` - Associated language
- `group` - Translation group
- `key` - Translation key
- `value` - Translation value
- `is_translated` - Translation complete flag
- `created_at`, `updated_at` - Timestamps

#### Translation Files
- `id` - Primary key
- `language_id` - Associated language
- `name` - File name
- `path` - File path
- `status` - File status
- `last_imported_at` - Last import timestamp
- `created_at`, `updated_at` - Timestamps

#### Timezones
- `id` - Primary key
- `name` - Timezone name (e.g., America/New_York)
- `offset` - UTC offset
- `country_code` - Country code
- `is_default` - Default timezone flag
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Languages

**List Languages:** `GET /api/tenant/localization/languages`
**Create Language:** `POST /api/tenant/localization/languages`
**Get Language:** `GET /api/tenant/localization/languages/{id}`
**Update Language:** `PUT /api/tenant/localization/languages/{id}`
**Delete Language:** `DELETE /api/tenant/localization/languages/{id}`
**Set Default Language:** `POST /api/tenant/localization/languages/{id}/set-default`
**Toggle Language Status:** `POST /api/tenant/localization/languages/{id}/toggle-status`

### Translations

**List Translations:** `GET /api/tenant/localization/translations`

**Query Parameters:**
- `language_id` - Filter by language
- `group` - Filter by group
- `search` - Search by key or value
- `untranslated` - Show only untranslated

**Create Translation:** `POST /api/tenant/localization/translations`
**Get Translation:** `GET /api/tenant/localization/translations/{id}`
**Update Translation:** `PUT /api/tenant/localization/translations/{id}`
**Delete Translation:** `DELETE /api/tenant/localization/translations/{id}`
**Import Translations:** `POST /api/tenant/localization/translations/import`
**Export Translations:** `GET /api/tenant/localization/translations/export`

### Timezones

**List Timezones:** `GET /api/tenant/localization/timezones`
**Get Timezone:** `GET /api/tenant/localization/timezones/{id}`
**Set Default Timezone:** `POST /api/tenant/localization/timezones/{id}/set-default`

### User Preferences

**Get User Locale:** `GET /api/tenant/localization/user/locale`
**Update User Locale:** `PUT /api/tenant/localization/user/locale`
**Get User Timezone:** `GET /api/tenant/localization/user/timezone`
**Update User Timezone:** `PUT /api/tenant/localization/user/timezone`

## Services

### LanguageService
- Language CRUD operations
- Default language management
- Language activation/deactivation
- Language statistics

### TranslationService
- Translation CRUD operations
- Translation import/export
- Translation key management
- Translation completion tracking

### TimezoneService
- Timezone data access
- Default timezone management
- Timezone conversion
- DST handling

### LocaleService
- Locale formatting
- Date/time localization
- Number/currency formatting
- Locale detection

## Repositories

### LanguageRepository
- Language data access
- Language filtering and searching
- Default language queries

### TranslationRepository
- Translation data access
- Translation filtering and searching
- Untranslated translation queries
- Group-based queries

### TimezoneRepository
- Timezone data access
- Timezone filtering and searching
- Country-based queries

## DTOs

### CreateLanguageData
Typed input transfer object for language creation with validation.

### UpdateLanguageData
Typed input transfer object for language updates with validation.

### CreateTranslationData
Typed input transfer object for translation creation with validation.

### UpdateUserLocaleData
Typed input transfer object for user locale updates with validation.

## Configuration

### Environment Variables

```env
# Localization Configuration
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_TIMEZONE=UTC
```

### Module Configuration

Module configuration in `Config/localization.php`:

```php
return [
    'languages' => [
        'default' => env('APP_LOCALE', 'en'),
        'fallback' => env('APP_FALLBACK_LOCALE', 'en'),
        'allowed' => ['en', 'es', 'fr', 'de', 'ar', 'zh'],
    ],
    'timezones' => {
        'default' => env('APP_TIMEZONE', 'UTC'),
    },
    'translations' => [
        'auto_import' => true,
        'export_format' => 'json',
        'translation_groups' => ['auth', 'validation', 'messages', 'ui'],
    ],
    'formatting' => [
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i:s',
        'datetime_format' => 'Y-m-d H:i:s',
        'currency_symbol' => '$',
    ],
];
```

## Translation Groups

Translations are organized into groups for better management:
- `auth` - Authentication-related translations
- `validation` - Validation messages
- `messages` - System messages
- `ui` - User interface text
- `emails` - Email templates
- `custom` - Custom translations

## Text Directions

- `ltr` - Left-to-right (English, Spanish, French, etc.)
- `rtl` - Right-to-left (Arabic, Hebrew, etc.)

## Locale Detection

The module supports automatic locale detection from:
- User preferences
- Browser Accept-Language header
- URL subdomain
- Query parameter

## Date/Time Formatting

Localized formatting includes:
- Date formats based on locale
- Time formats based on locale
- Timezone conversion
- Relative time (e.g., "2 hours ago")

## Number Formatting

Localized number formatting includes:
- Decimal separators
- Thousand separators
- Currency formatting
- Percentage formatting

## Permissions

Localization module permissions follow the pattern: `localization.{resource}.{action}`

- `localization.languages.view` - View languages
- `localization.languages.create` - Create languages
- `localization.languages.edit` - Edit languages
- `localization.languages.delete` - Delete languages
- `localization.translations.view` - View translations
- `localization.translations.create` - Create translations
- `localization.translations.edit` - Edit translations
- `localization.translations.delete` - Delete translations
- `localization.translations.import` - Import translations
- `localization.translations.export` - Export translations
- `localization.timezones.view` - View timezones
- `localization.preferences.manage` - Manage user preferences

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Localization/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Translation import/export tests
- Locale formatting tests

## Related Documentation

- [Laravel Localization](https://laravel.com/docs/localization)
- [ISO 639 Language Codes](https://www.iso.org/iso-639-language-codes)
