# Geography Module Documentation

## Overview

The Geography module provides comprehensive geographic data management including countries, regions, cities, and addresses. It enables location-based features, address validation, and geographic filtering across the platform.

## Architecture

### Module Structure

```
Geography/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # Geography entities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Resources/           # API resources
├── Routes/              # API and web routes
├── Services/            # Business logic services
├── composer.json        # Composer dependencies
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Countries
- `id` - Primary key
- `name` - Country name
- `code` - ISO 3166-1 alpha-2 code
- `code3` - ISO 3166-1 alpha-3 code
- `numeric_code` - ISO 3166-1 numeric code
- `phone_code` - International phone code
- `capital` - Capital city
- `currency` - Currency code
- `currency_name` - Currency name
- `currency_symbol` - Currency symbol
- `region` - Geographic region
- `subregion` - Geographic subregion
- `latitude` - Latitude coordinate
- `longitude` - Longitude coordinate
- `flag` - Flag emoji or URL
- `status` - Country status
- `created_at`, `updated_at` - Timestamps

#### Regions
- `id` - Primary key
- `country_id` - Associated country
- `name` - Region name
- `code` - Region code
- `abbreviation` - Region abbreviation
- `latitude` - Latitude coordinate
- `longitude` - Longitude coordinate
- `status` - Region status
- `created_at`, `updated_at` - Timestamps

#### Cities
- `id` - Primary key
- `country_id` - Associated country
- `region_id` - Associated region
- `name` - City name
- `latitude` - Latitude coordinate
- `longitude` - Longitude coordinate
- `population` - Population count
- `timezone` - Timezone
- `status` - City status
- `created_at`, `updated_at` - Timestamps

#### Addresses
- `id` - Primary key
- `addressable_type` - Entity type (polymorphic)
- `addressable_id` - Entity ID (polymorphic)
- `country_id` - Associated country
- `region_id` - Associated region
- `city_id` - Associated city
- `address_line1` - Address line 1
- `address_line2` - Address line 2
- `postal_code` - Postal/ZIP code
- `latitude` - Latitude coordinate
- `longitude` - Longitude coordinate
- `is_primary` - Primary address flag
- `is_billing` - Billing address flag
- `is_shipping` - Shipping address flag
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Countries

**List Countries:** `GET /api/tenant/geography/countries`
**Get Country:** `GET /api/tenant/geography/countries/{id}`
**Search Countries:** `GET /api/tenant/geography/countries/search`

### Regions

**List Regions:** `GET /api/tenant/geography/regions`

**Query Parameters:**
- `country_id` - Filter by country
- `search` - Search by name

**Get Region:** `GET /api/tenant/geography/regions/{id}`
**Get Country Regions:** `GET /api/tenant/geography/countries/{countryId}/regions`

### Cities

**List Cities:** `GET /api/tenant/geography/cities`

**Query Parameters:**
- `country_id` - Filter by country
- `region_id` - Filter by region
- `search` - Search by name

**Get City:** `GET /api/tenant/geography/cities/{id}`
**Get Region Cities:** `GET /api/tenant/geography/regions/{regionId}/cities`

### Addresses

**List Addresses:** `GET /api/tenant/geography/addresses`

**Query Parameters:**
- `addressable_type` - Filter by entity type
- `addressable_id` - Filter by entity ID
- `country_id` - Filter by country

**Create Address:** `POST /api/tenant/geography/addresses`
**Get Address:** `GET /api/tenant/geography/addresses/{id}`
**Update Address:** `PUT /api/tenant/geography/addresses/{id}`
**Delete Address:** `DELETE /api/tenant/geography/addresses/{id}`
**Set Primary Address:** `POST /api/tenant/geography/addresses/{id}/set-primary`
**Validate Address:** `POST /api/tenant/geography/addresses/validate`

## Services

### CountryService
- Country data access
- Country search and filtering
- Country validation

### RegionService
- Region data access
- Region-country relationships
- Region search and filtering

### CityService
- City data access
- City-region-country relationships
- City search and filtering
- Geographic proximity queries

### AddressService
- Address CRUD operations
- Address validation
- Geocoding
- Address formatting

## Repositories

### CountryRepository
- Country data access
- Country filtering and searching
- ISO code queries

### RegionRepository
- Region data access
- Region filtering and searching
- Country-region relationships

### CityRepository
- City data access
- City filtering and searching
- Region-city relationships

### AddressRepository
- Address data access
- Address filtering and searching
- Polymorphic queries
- Primary address queries

## DTOs

### CreateAddressData
Typed input transfer object for address creation with validation.

### UpdateAddressData
Typed input transfer object for address updates with validation.

### GeocodeData
Typed input transfer object for geocoding requests.

## Configuration

### Module Configuration

Module configuration in `Config/geography.php`:

```php
return [
    'countries' => [
        'default_country' => 'US',
        'allowed_countries' => [], // Empty means all countries allowed
    ],
    'addresses' => {
        'require_validation' => false,
        'geocoding_enabled' => true,
        'geocoding_provider' => 'google', // google, mapbox, openstreetmap
    },
    'caching' => [
        'enabled' => true,
        'ttl' => 86400, // 24 hours
    ],
];
```

## Address Types

- `primary` - Primary address
- `billing` - Billing address
- `shipping` - Shipping address

## ISO Standards

The module follows ISO 3166-1 standards for country codes:
- Alpha-2 codes (2 letters): US, GB, DE, FR
- Alpha-3 codes (3 letters): USA, GBR, DEU, FRA
- Numeric codes (3 digits): 840, 826, 276, 250

## Geocoding

The module supports geocoding addresses to latitude/longitude coordinates:
- Forward geocoding: Address → Coordinates
- Reverse geocoding: Coordinates → Address
- Geocoding providers: Google Maps, Mapbox, OpenStreetMap

## Address Validation

Address validation includes:
- Country validation (ISO codes)
- Region validation (must belong to country)
- City validation (must belong to country/region)
- Postal code format validation
- Address line format validation

## Permissions

Geography module permissions follow the pattern: `geography.{resource}.{action}`

- `geography.countries.view` - View countries
- `geography.regions.view` - View regions
- `geography.cities.view` - View cities
- `geography.addresses.view` - View addresses
- `geography.addresses.create` - Create addresses
- `geography.addresses.edit` - Edit addresses
- `geography.addresses.delete` - Delete addresses
- `geography.addresses.validate` - Validate addresses

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/Geography/Tests --testdox
```

Test coverage includes:
- Unit tests for services
- Feature tests for API endpoints
- Address validation tests
- Geocoding tests

## Related Documentation

- [ISO 3166 Standards](https://www.iso.org/iso-3166-country-codes)
- [Address Validation Guide](../../backend/documentation/geography/validation.md)
