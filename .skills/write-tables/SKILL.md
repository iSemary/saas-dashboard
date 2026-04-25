---
description: Write table components with search, sort, pagination, and CRUD operations
tags: [frontend, backend, tables, crud, react, laravel]
---

# Table Components Skill

This skill covers creating and modifying table components in both frontend (React/Next.js) and backend (Laravel) environments.

## Frontend Components

### DataTable Component
Location: `src/components/data-table.tsx`

**Props Interface:**
```typescript
interface Props<TData> {
  columns: Array<ColumnDef<TData>>;
  data: TData[];
  toolbarActions?: ReactNode;
  enableExport?: boolean;
  searchable?: boolean;
  serverSide?: boolean;        // Enable backend handling
  pageCount?: number;          // Required when serverSide=true
  meta?: TableMeta;            // Backend pagination metadata
  loading?: boolean;
  onTableChange?: (params: {
    page: number;
    perPage: number;
    search: string;
    sortBy: string | null;
    sortDirection: 'asc' | 'desc';
  }) => void;
}
```

**Features:**
- Icon buttons with tooltips (Export Excel/CSV, Columns)
- Single global search input (filters columns with `meta.searchable`)
- Sortable column headers (click to toggle asc/desc)
- Items per page selector: 10, 25, 50, 100, All
- Numbered pagination with First/Last buttons
- Empty state with "No records found" message
- Loading overlay for server-side operations
- Row selection with checkboxes

**Column Definition Example:**
```typescript
const columns: ColumnDef<Brand>[] = [
  { 
    accessorKey: "name", 
    header: "Name", 
    meta: { searchable: true, sortable: true } 
  },
  { 
    accessorKey: "email", 
    header: "Email", 
    meta: { searchable: true, sortable: false } 
  },
];
```

### SimpleCRUDPage Component
Location: `src/components/simple-crud-page.tsx`

**Configuration Interface:**
```typescript
interface SimpleCRUDConfig<T> {
  titleKey: string;
  titleFallback: string;
  subtitleKey: string;
  subtitleFallback: string;
  createLabelKey: string;
  createLabelFallback: string;
  fields: FieldDef[];
  listFn: (params?: TableParams) => Promise<T[]> | Promise<PaginatedResponse<T>>;
  createFn: (payload: Record<string, unknown>) => Promise<unknown>;
  updateFn?: (id: number, payload: Record<string, unknown>) => Promise<unknown>;
  deleteFn?: (id: number) => Promise<void>;
  columns: (t) => Array<ColumnDef<T>>;
  toForm: (row: T) => Record<string, string>;
  fromForm: (form: Record<string, string>) => Record<string, unknown>;
  serverSide?: boolean;           // Enable backend pagination/search/sort
  searchableColumns?: string[]; // Backend searchable fields
  sortableColumns?: string[];    // Backend sortable fields
}
```

**Field Definition Types:**
- `text` - Standard text input
- `email` - Email input with validation
- `password` - Password input
- `number` - Numeric input
- `url` - URL input
- `textarea` - Multi-line text
- `select` - Dropdown with options
- `richtext` - Rich text editor (CKEditor)
- `slug` - Auto-generated slug from source field

**Usage Example:**
```typescript
const config = {
  titleKey: "dashboard.brands.title",
  titleFallback: "Brands",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "name" },
  ],
  listFn: listBrands,
  createFn: createBrand,
  updateFn: updateBrand,
  deleteFn: deleteBrand,
  serverSide: true,  // Enable backend handling
};

export default function BrandsPage() {
  return <SimpleCRUDPage config={config} />;
}
```

## Backend Components

### TableListRequest
Location: `app/Http/Requests/TableListRequest.php`

**Validates and provides defaults for:**
- `page` - Current page (default: 1)
- `per_page` - Items per page (default: 10, max: 100, supports 'all')
- `search` - Search query string
- `sort_by` - Column to sort by
- `sort_direction` - asc or desc (default: asc)
- `filters` - Additional filter array

**Usage in Controller:**
```php
use App\Http\Requests\TableListRequest;

public function index(TableListRequest $request)
{
    $params = $request->getTableParams();
    // Returns: ['page' => 1, 'per_page' => 10, 'search' => null, ...]
}
```

### TableListTrait
Location: `app/Repositories/Traits/TableListTrait.php`

**Methods:**

1. `tableList($modelClass, $params, $searchableColumns, $sortableColumns)`
   - One-line complete table listing
   - Returns: `LengthAwarePaginator` or `Collection`

2. `applyTableOperations($query, $params, $searchableColumns, $sortableColumns)`
   - Applies search, filters, and sorting to query builder

3. `getResults($query, $params)`
   - Returns paginated results or all records based on `per_page`

**Repository Usage:**
```php
use App\Repositories\Traits\TableListTrait;

class BrandRepository
{
    use TableListTrait;

    public function list(array $params)
    {
        return $this->tableList(
            Brand::class,
            $params,
            ['name' => 'name', 'slug' => 'slug'],  // searchable
            ['id' => 'id', 'name' => 'name']      // sortable
        );
    }
}
```

### Controller Response
Use `apiPaginated()` from `ApiResponseEnvelope` trait:

```php
use App\Http\Controllers\ApiResponseEnvelope;

class BrandController extends Controller
{
    use ApiResponseEnvelope;

    public function index(TableListRequest $request)
    {
        $params = $request->getTableParams();
        $results = $this->repository->list($params);
        
        return $this->apiPaginated($results);
        // Returns: { status: "success", data: [...], meta: {...} }
    }
}
```

## API Resource Functions

### Tenant Frontend
Location: `src/lib/tenant-resources.ts`

```typescript
import type { TableParams, PaginatedResponse } from "@/lib/tenant-resources";

// List functions now accept TableParams
export const listBrands = (params?: TableParams) => 
  api.get(`${T}/brands${buildTableQuery(params)}`)
     .then((r) => r.data?.data ?? r.data as unknown[]);
```

### Landlord Frontend  
Location: `src/lib/resources.ts`

```typescript
import type { TableParams, PaginatedResponse } from "@/lib/resources";

// Same pattern as tenant frontend
export const listUsers = (params?: TableParams) => 
  api.get(`/users${buildTableQuery(params)}`)
     .then((r) => r.data?.data ?? r.data as unknown[]);
```

## Key Conventions

1. **Search Behavior:**
   - Frontend: Only searches columns with `meta.searchable = true`
   - Backend: Uses `LIKE %search%` on configured searchable columns

2. **Sort Behavior:**
   - Frontend: Click column header to toggle asc/desc/none
   - Backend: Only sorts columns in `sortableColumns` map

3. **Pagination:**
   - Client-side: Uses TanStack Table's pagination
   - Server-side: Uses Laravel's `paginate()` with `manualPagination: true`

4. **Per Page "All":**
   - Sends `per_page=all` to backend
   - Backend returns full collection without pagination

5. **Response Format:**
   ```json
   {
     "status": "success",
     "data": [...],
     "meta": {
       "current_page": 1,
       "last_page": 5,
       "per_page": 10,
       "total": 50
     }
   }
   ```
