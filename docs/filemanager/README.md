# FileManager Module Documentation

## Overview

The FileManager module provides comprehensive file management functionality including file upload, download, organization, sharing, and storage management. It enables users to store, organize, and share files across the platform with support for various file types and storage backends.

## Architecture

### Module Structure

```
FileManager/
├── Config/              # Module configuration
├── DTOs/                # Data transfer objects
├── Entities/            # File entities
├── Http/                # HTTP layer
│   └── Controllers/     # API controllers
├── Providers/           # Service providers
├── Repositories/        # Data access layer
├── Routes/              # API and web routes
├── Services/            # Business logic services
├── Traits/              # Reusable traits
└── database/            # Database migrations
```

## Database Schema

### Core Entities

#### Files
- `id` - Primary key
- `fileable_type` - Entity type (polymorphic)
- `fileable_id` - Entity ID (polymorphic)
- `user_id` - User who uploaded file
- `name` - File name
- `original_name` - Original file name
- `path` - File path
- `disk` - Storage disk
- `mime_type` - MIME type
- `size` - File size in bytes
- `extension` - File extension
- `category` - File category
- `status` - File status (active, archived, deleted)
- `created_at`, `updated_at` - Timestamps

#### File Shares
- `id` - Primary key
- `file_id` - Associated file
- `shared_by` - User who shared
- `shared_with` - User who received share (nullable for public links)
- `share_type` - Share type (user, public, link)
- `share_token` - Share token for public links
- `expires_at` - Expiration timestamp
- `permission` - Permission (view, download, edit)
- `download_count` - Download count
- `created_at`, `updated_at` - Timestamps

#### File Folders
- `id` - Primary key
- `parent_id` - Parent folder ID (for hierarchy)
- `name` - Folder name
- `path` - Folder path
- `user_id` - Folder owner
- `is_public` - Public folder flag
- `created_at`, `updated_at` - Timestamps

## API Endpoints

### Files

**List Files:** `GET /api/tenant/files`

**Query Parameters:**
- `fileable_type` - Filter by entity type
- `fileable_id` - Filter by entity ID
- `category` - Filter by category
- `mime_type` - Filter by MIME type
- `search` - Search by name

**Upload File:** `POST /api/tenant/files`
**Get File:** `GET /api/tenant/files/{id}`
**Update File:** `PUT /api/tenant/files/{id}`
**Delete File:** `DELETE /api/tenant/files/{id}`
**Download File:** `GET /api/tenant/files/{id}/download`
**Archive File:** `POST /api/tenant/files/{id}/archive`
**Restore File:** `POST /api/tenant/files/{id}/restore`

### File Shares

**List Shares:** `GET /api/tenant/file-shares`
**Create Share:** `POST /api/tenant/file-shares`
**Get Share:** `GET /api/tenant/file-shares/{id}`
**Delete Share:** `DELETE /api/tenant/file-shares/{id}`
**Get Public Share:** `GET /api/tenant/file-shares/public/{token}`
**Revoke Share:** `POST /api/tenant/file-shares/{id}/revoke`

### File Folders

**List Folders:** `GET /api/tenant/file-folders`
**Create Folder:** `POST /api/tenant/file-folders`
**Get Folder:** `GET /api/tenant/file-folders/{id}`
**Update Folder:** `PUT /api/tenant/file-folders/{id}`
**Delete Folder:** `DELETE /api/tenant/file-folders/{id}`
**Get Folder Files:** `GET /api/tenant/file-folders/{id}/files`

## Services

### FileService
- File upload handling
- File validation
- File storage management
- File deletion and cleanup

### FileShareService
- Share creation and management
- Share token generation
- Share permission handling
- Share expiration management

### FileFolderService
- Folder CRUD operations
- Folder hierarchy management
- Folder-file associations
- Folder permission handling

## Repositories

### FileRepository
- File data access
- File filtering and searching
- Polymorphic queries
- File-folder relationships

### FileShareRepository
- Share data access
- Share filtering and searching
- Expiration queries
- User share queries

### FileFolderRepository
- Folder data access
- Folder hierarchy queries
- Folder-user relationships
- Folder-file count queries

## DTOs

### UploadFileData
Typed input transfer object for file upload with validation.

### CreateShareData
Typed input transfer object for share creation with validation.

### CreateFolderData
Typed input transfer object for folder creation with validation.

## Configuration

### Environment Variables

```env
# File Storage Configuration
FILESYSTEM_DISK=local
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket
AWS_URL=https://your-bucket.s3.amazonaws.com
```

### Module Configuration

Module configuration in `Config/filemanager.php`:

```php
return [
    'storage' => [
        'default_disk' => env('FILESYSTEM_DISK', 'local'),
        'allowed_disks' => ['local', 's3', 'public'],
    ],
    'upload' => [
        'max_file_size' => 102400, // KB (100MB)
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'txt'],
        'allowed_mime_types' => ['image/*', 'application/pdf', 'application/msword', 'application/vnd.ms-excel', 'application/zip', 'text/*'],
    ],
    'shares' => [
        'default_expiry_days' => 7,
        'max_expiry_days' => 30,
        'allow_public_shares' => true,
    ],
    'folders' => [
        'max_depth' => 5,
        'max_files_per_folder' => 1000,
    ],
];
```

## Storage Disks

### Local Storage
- Files stored in `storage/app/public`
- Suitable for development and small deployments
- No external dependencies

### S3 Storage
- Files stored in AWS S3
- Suitable for production and large deployments
- Scalable and reliable
- CDN integration support

### Public Storage
- Files stored in `storage/app/public` with public access
- Suitable for publicly accessible files
- Direct URL access

## File Categories

- `documents` - Documents (PDF, DOC, DOCX)
- `images` - Images (JPG, PNG, GIF, SVG)
- `videos` - Videos (MP4, AVI, MOV)
- `audio` - Audio files (MP3, WAV)
- `archives` - Archives (ZIP, RAR, TAR)
- `data` - Data files (CSV, JSON, XML)
- `other` - Other file types

## Share Types

- `user` - Shared with specific user
- `public` - Publicly accessible via link
- `link` - Shareable link with optional password

## Share Permissions

- `view` - View file only
- `download` - View and download file
- `edit` - View, download, and edit file

## Business Rules

- File size is limited by configuration
- File extensions are validated against allowed list
- Public shares require token authentication
- Shared files track download counts
- Folders support hierarchical structure
- Deleted files can be restored within grace period
- Polymorphic associations allow files to be attached to any entity

## Permissions

FileManager module permissions follow the pattern: `filemanager.{resource}.{action}`

- `filemanager.files.view` - View files
- `filemanager.files.upload` - Upload files
- `filemanager.files.edit` - Edit files
- `filemanager.files.delete` - Delete files
- `filemanager.files.download` - Download files
- `filemanager.shares.view` - View shares
- `filemanager.shares.create` - Create shares
- `filemanager.shares.delete` - Delete shares
- `filemanager.folders.view` - View folders
- `filemanager.folders.create` - Create folders
- `filemanager.folders.edit` - Edit folders
- `filemanager.folders.delete` - Delete folders

## Testing

Module tests in `Tests/`:

```bash
php artisan test modules/FileManager/Tests --testdox
```

Test coverage includes:
- Unit tests for file upload
- Feature tests for API endpoints
- Share functionality tests
- Folder hierarchy tests

## Related Documentation

- [File Storage Configuration](../../backend/documentation/filemanager/storage.md)
- [File Security Guide](../../backend/documentation/filemanager/security.md)
