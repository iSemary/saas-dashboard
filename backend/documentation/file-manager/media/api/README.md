# Media API Documentation

## Overview

The Media API provides centric file upload endpoints for the SaaS Dashboard. It supports single file upload, bulk upload (up to 10 files), retrieval, and deletion. All endpoints require authentication.

The API reuses the existing `FileManager` module's `File` entity and storage infrastructure (local disk or AWS S3), and adds structured validation for mime types, extensions, and per-type size limits.

## Authentication

All endpoints require authentication:

- `Authorization: Bearer {token}` — API token authentication via `auth:api` middleware

## Configuration

Media validation settings are configured in `modules/FileManager/Config/config.php` and can be overridden via `.env`:

| Config Key | Env Variable | Default | Description |
|---|---|---|---|
| `filemanager.media.allowed_extensions` | — | jpg, jpeg, png, gif, webp, heic, heif, bmp, tiff, svg, mp4, mov, webm, m4v, 3gp, pdf, doc, docx, xls, xlsx, csv, txt, zip | Allowed file extensions |
| `filemanager.media.max_photo_size` | `MEDIA_MAX_PHOTO_SIZE` | 10240 (10 MB) | Max photo size in KB |
| `filemanager.media.max_video_size` | `MEDIA_MAX_VIDEO_SIZE` | 51200 (50 MB) | Max video size in KB |
| `filemanager.media.max_document_size` | `MEDIA_MAX_DOCUMENT_SIZE` | 20480 (20 MB) | Max document size in KB |
| `filemanager.media.max_bulk_count` | `MEDIA_MAX_BULK_COUNT` | 10 | Max files per bulk upload |

## Endpoints

---

### Upload Single File

**Endpoint:** `POST /api/media/upload`

**Description:** Upload a single file and create a media record. Returns the media record with its URL.

**Request Body (multipart/form-data):**

| Field | Type | Required | Description |
|---|---|---|---|
| `file` | file | yes | The file to upload |
| `folder_id` | int | no | ID of the folder to store in |
| `access_level` | string | no | `public` or `private` (default: `public`) |

**Response (201):**
```json
{
  "status": 201,
  "success": true,
  "message": "File uploaded successfully",
  "data": {
    "id": 1,
    "folder_id": null,
    "hash_name": "abc123def456.jpg",
    "checksum": "sha256hash...",
    "original_name": "photo.jpg",
    "mime_type": "image/jpeg",
    "host": "local",
    "status": "active",
    "access_level": "public",
    "size": 204800,
    "metadata": {
      "uploaded_by": 1,
      "uploaded_at": "2025-01-15T10:30:00Z",
      "storage_key": "media/2025/01/uuid.jpg"
    },
    "is_encrypted": false,
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T10:30:00Z",
    "url": "http://localhost:8000/storage/media/2025/01/uuid.jpg"
  }
}
```

**Error Responses:**

- **422** — Validation failed (no file, unsupported type, extension not allowed, size exceeded)
- **500** — Server error

---

### Upload Multiple Files (Bulk)

**Endpoint:** `POST /api/media/upload/bulk`

**Description:** Upload multiple files at once (max 10 by default). Each file is validated individually. Returns an array of media records with URLs.

**Request Body (multipart/form-data):**

| Field | Type | Required | Description |
|---|---|---|---|
| `files[]` | file[] | yes | Array of files to upload (1–10) |
| `folder_id` | int | no | ID of the folder to store in |
| `access_level` | string | no | `public` or `private` (default: `public`) |

**Response (201):**
```json
{
  "status": 201,
  "success": true,
  "message": "Files uploaded successfully",
  "data": [
    {
      "id": 1,
      "folder_id": null,
      "hash_name": "abc123.jpg",
      "checksum": "sha256hash...",
      "original_name": "photo1.jpg",
      "mime_type": "image/jpeg",
      "host": "local",
      "status": "active",
      "access_level": "public",
      "size": 204800,
      "metadata": {
        "uploaded_by": 1,
        "uploaded_at": "2025-01-15T10:30:00Z",
        "storage_key": "media/2025/01/uuid1.jpg"
      },
      "is_encrypted": false,
      "created_at": "2025-01-15T10:30:00Z",
      "updated_at": "2025-01-15T10:30:00Z",
      "url": "http://localhost:8000/storage/media/2025/01/uuid1.jpg"
    },
    {
      "id": 2,
      "folder_id": null,
      "hash_name": "def456.png",
      "checksum": "sha256hash...",
      "original_name": "photo2.png",
      "mime_type": "image/png",
      "host": "local",
      "status": "active",
      "access_level": "public",
      "size": 307200,
      "metadata": {
        "uploaded_by": 1,
        "uploaded_at": "2025-01-15T10:30:01Z",
        "storage_key": "media/2025/01/uuid2.png"
      },
      "is_encrypted": false,
      "created_at": "2025-01-15T10:30:01Z",
      "updated_at": "2025-01-15T10:30:01Z",
      "url": "http://localhost:8000/storage/media/2025/01/uuid2.png"
    }
  ]
}
```

**Error Responses:**

- **422** — Validation failed (no files, too many files, unsupported type, extension not allowed, size exceeded)
- **500** — Server error

---

### Get Media by ID

**Endpoint:** `GET /api/media/{id}`

**Description:** Retrieve a media record by its ID, including the generated URL.

**URL Parameters:**

| Parameter | Type | Description |
|---|---|---|
| `id` | int | The media record ID |

**Response (200):**
```json
{
  "status": 200,
  "success": true,
  "message": "OK",
  "data": {
    "id": 1,
    "folder_id": null,
    "hash_name": "abc123def456.jpg",
    "checksum": "sha256hash...",
    "original_name": "photo.jpg",
    "mime_type": "image/jpeg",
    "host": "local",
    "status": "active",
    "access_level": "public",
    "size": 204800,
    "metadata": {
      "uploaded_by": 1,
      "uploaded_at": "2025-01-15T10:30:00Z",
      "storage_key": "media/2025/01/uuid.jpg"
    },
    "is_encrypted": false,
    "created_at": "2025-01-15T10:30:00Z",
    "updated_at": "2025-01-15T10:30:00Z",
    "folder": null,
    "url": "http://localhost:8000/storage/media/2025/01/uuid.jpg"
  }
}
```

**Error Responses:**

- **404** — Media not found
- **500** — Server error

---

### Delete Media

**Endpoint:** `DELETE /api/media/{id}`

**Description:** Delete a media record and its physical file from storage.

**URL Parameters:**

| Parameter | Type | Description |
|---|---|---|
| `id` | int | The media record ID |

**Response (200):**
```json
{
  "status": 200,
  "success": true,
  "message": "File deleted successfully"
}
```

**Error Responses:**

- **404** — Media not found
- **500** — Server error

---

## Storage

Files are stored based on the configured default host:

- **Local** — Files are stored in `storage/app/public/{folder}/{year}/{month}/{uuid}.{ext}` and served via `storage:link`
- **AWS S3** — Files are stored in the configured S3 bucket under `{folder}/{year}/{month}/{uuid}.{ext}` and served via signed URLs

The host is determined by the `file_manager.default_host` configuration value.

## Validation Rules

### Mime Type & Extension

Only the following file categories are accepted:

- **Images:** jpg, jpeg, png, gif, webp, heic, heif, bmp, tiff, svg
- **Videos:** mp4, mov, webm, m4v, 3gp
- **Documents:** pdf, doc, docx, xls, xlsx, csv, txt, zip

The file extension must match the actual content type (mime type). For example, a `.txt` file with `image/jpeg` mime type will be rejected.

### Size Limits

| Type | Default Max Size | Config Key |
|---|---|---|
| Photos | 10 MB | `filemanager.media.max_photo_size` |
| Videos | 50 MB | `filemanager.media.max_video_size` |
| Documents | 20 MB | `filemanager.media.max_document_size` |

### Bulk Upload

- Maximum 10 files per request (configurable via `filemanager.media.max_bulk_count`)
- Each file is individually validated
