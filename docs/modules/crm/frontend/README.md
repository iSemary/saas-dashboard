# CRM Module — Frontend Documentation

## Overview

The CRM frontend is a set of Next.js (App Router) pages living under:
```
tenant-frontend/src/app/dashboard/modules/crm/
```

All pages are **client components** (`"use client"`) and are rendered inside the shared dashboard layout. CRUD pages use the project-wide `SimpleCRUDPage` generic component. The pipeline/deals views are custom implementations.

---

## Pages

### `/dashboard/modules/crm` — Dashboard

**File**: `page.tsx`  
**Purpose**: Overview of CRM KPIs (total leads, open opportunities, recent activities, win rate)  
**API**: `GET /tenant/crm/dashboard` via `getCrmDashboard()` from `tenant-resources.ts`

---

### `/dashboard/modules/crm/leads` — Leads

**File**: `leads/page.tsx`  
**Pattern**: `SimpleCRUDPage<Lead>` with `serverSide: true`

**Columns**:

| Column | Key | Notes |
|---|---|---|
| Name | `name` | Plain text |
| Email | `email` | Nullable, shows `—` |
| Phone | `phone` | Nullable |
| Status | `status` | Badge (new/contacted/qualified/converted/lost) |
| Source | `source` | Nullable |
| Value | `value` | Currency formatted |

**Form Fields**: `name` (required), `email`, `phone`, `status` (select), `source` (select), `value`, `description` (textarea)

**API functions**:
- `listCrmLeads(params)` — `GET /tenant/crm/leads`
- `createCrmLead(data)` — `POST /tenant/crm/leads`
- `updateCrmLead(id, data)` — `PUT /tenant/crm/leads/{id}`
- `deleteCrmLead(id)` — `DELETE /tenant/crm/leads/{id}`

---

### `/dashboard/modules/crm/opportunities` — Opportunities

**File**: `opportunities/page.tsx`  
**Pattern**: `SimpleCRUDPage<Opportunity>` with `serverSide: true`

**Columns**:

| Column | Key | Notes |
|---|---|---|
| Name | `name` | Plain text |
| Stage | `stage` | Badge with colour per stage |
| Revenue | `expected_revenue` | Currency formatted |
| Probability | `probability` | Percentage |
| Close Date | `expected_close_date` | Date |

**Stage badge colours**: `prospecting` → slate, `qualification` → blue, `proposal` → yellow, `negotiation` → orange, `closed_won` → green, `closed_lost` → red

**Form Fields**: `name` (required), `stage` (select), `expected_revenue`, `probability`, `expected_close_date`, `description` (textarea)

**API functions**:
- `listCrmOpportunities(params)`
- `createCrmOpportunity(data)`
- `updateCrmOpportunity(id, data)`
- `deleteCrmOpportunity(id)`

---

### `/dashboard/modules/crm/contacts` — Contacts

**File**: `contacts/page.tsx`  
**Pattern**: `SimpleCRUDPage<Contact>` with `serverSide: true`

**Columns**:

| Column | Key | Notes |
|---|---|---|
| Name | computed | `first_name + last_name` via cell renderer |
| Email | `email` | Nullable |
| Phone | `phone` | Nullable |
| Title | `title` | Nullable |

**Form Fields**: `first_name` (required), `last_name` (required), `email`, `phone`, `title`, `description` (textarea)

**API functions**:
- `listCrmContacts(params)`
- `createCrmContact(data)`
- `updateCrmContact(id, data)`
- `deleteCrmContact(id)`

---

### `/dashboard/modules/crm/companies` — Companies

**File**: `companies/page.tsx`  
**Pattern**: `SimpleCRUDPage<Company>` with `serverSide: true`

**Columns**:

| Column | Key | Notes |
|---|---|---|
| Name | `name` | — |
| Email | `email` | Nullable |
| Phone | `phone` | Nullable |
| Industry | `industry` | Nullable |
| Type | `type` | Nullable |
| Website | `website` | Clickable `<a>` link, opens in new tab |

**Form Fields**: `name` (required), `email`, `phone`, `website` (url), `industry` (select), `type` (select), `description` (textarea)

**Industry options**: `technology`, `finance`, `healthcare`, `retail`, `manufacturing`, `education`, `real_estate`, `other`  
**Type options**: `prospect`, `customer`, `partner`, `vendor`

**API functions**:
- `listCrmCompanies(params)`
- `createCrmCompany(data)`
- `updateCrmCompany(id, data)`
- `deleteCrmCompany(id)`

---

### `/dashboard/modules/crm/activities` — Activities

**File**: `activities/page.tsx`  
**Pattern**: `SimpleCRUDPage<Activity>` with `serverSide: true`

**Columns**:

| Column | Key | Notes |
|---|---|---|
| Subject | `subject` | — |
| Type | `type` | Icon + badge (call/email/meeting/task/note) |
| Status | `status` | Badge |
| Due Date | `due_date` | Date |
| Assigned To | `assigned_to` | User ID or name |

**Form Fields**: `subject` (required), `type` (select), `status` (select), `due_date`, `description` (textarea)

**API functions**:
- `listCrmActivities(params)`
- `createCrmActivity(data)`
- `updateCrmActivity(id, data)`
- `deleteCrmActivity(id)`

---

### `/dashboard/modules/crm/deals` — Deals (Kanban Board)

**File**: `deals/page.tsx`  
**Pattern**: Custom Kanban using **native HTML5 drag-and-drop** (`draggable` + `onDragStart` / `onDragOver` / `onDrop`)

#### Architecture

```
KanbanColumn[]           ← state, initialised from getCrmPipeline()
  ↓
  stage, label, color, count, value, probability
  opportunities: Deal[]  ← cards within each column
```

#### Drag-and-Drop Flow

1. `onDragStart` stores `{ id, fromStage }` in a `useRef` (avoids re-renders)
2. `onDrop` on a column triggers:
   - **Optimistic update**: moves the card in local state immediately
   - `moveCrmOpportunityStage(dealId, toStage)` API call
   - On error: `reload()` reverts to server state via `getCrmPipeline()`

#### Stage Header Colours

| Stage | Border colour |
|---|---|
| `prospecting` | slate-400 |
| `qualification` | blue-400 |
| `proposal` | yellow-400 |
| `negotiation` | orange-400 |
| `closed_won` | green-500 |
| `closed_lost` | red-400 |

#### API functions used
- `getCrmPipeline()` — loads all columns + deals
- `moveCrmOpportunityStage(id, stage)` — `POST /tenant/crm/opportunities/{id}/move-stage`

---

### `/dashboard/modules/crm/pipeline` — Pipeline Overview

**File**: `pipeline/page.tsx`  
**Pattern**: Custom read-only view (no drag-and-drop)  
**Purpose**: Shows all pipeline stages as cards with deal count, total value, and probability per stage

**API**: `getCrmPipeline()` — same endpoint as Deals page

---

## API Functions (`tenant-resources.ts`)

All CRM functions are defined in `tenant-frontend/src/lib/tenant-resources.ts` and use the project's Axios instance with automatic tenant auth headers.

### Return Types

List functions return `Promise<PaginatedResponse<T>>`:
```ts
interface PaginatedResponse<T> {
  data: T[];
  meta: { current_page: number; last_page: number; total: number; per_page: number };
}
```

Delete functions return `Promise<void>` (chain `.then(() => undefined)`).

### Complete Function Reference

```ts
// Leads
listCrmLeads(params?)          → PaginatedResponse<Lead>
getCrmLead(id)                 → Lead
createCrmLead(data)            → Lead
updateCrmLead(id, data)        → Lead
deleteCrmLead(id)              → void
convertCrmLead(id, data)       → { lead, opportunity }

// Opportunities
listCrmOpportunities(params?)  → PaginatedResponse<Opportunity>
getCrmOpportunity(id)          → Opportunity
createCrmOpportunity(data)     → Opportunity
updateCrmOpportunity(id, data) → Opportunity
deleteCrmOpportunity(id)       → void
getCrmPipeline()               → KanbanColumn[]
moveCrmOpportunityStage(id, stage) → Opportunity
closeCrmOpportunityWon(id)     → Opportunity

// Contacts
listCrmContacts(params?)       → PaginatedResponse<Contact>
getCrmContact(id)              → Contact
createCrmContact(data)         → Contact
updateCrmContact(id, data)     → Contact
deleteCrmContact(id)           → void

// Companies
listCrmCompanies(params?)      → PaginatedResponse<Company>
getCrmCompany(id)              → Company
createCrmCompany(data)         → Company
updateCrmCompany(id, data)     → Company
deleteCrmCompany(id)           → void

// Activities
listCrmActivities(params?)     → PaginatedResponse<Activity>
getCrmActivity(id)             → Activity
createCrmActivity(data)        → Activity
updateCrmActivity(id, data)    → Activity
deleteCrmActivity(id)          → void

// Notes
listCrmNotes(params?)          → PaginatedResponse<Note>
createCrmNote(data)            → Note
updateCrmNote(id, data)        → Note
deleteCrmNote(id)              → void
getCrmNotesForEntity(type, id) → Note[]

// Files
listCrmFiles(params?)          → PaginatedResponse<CrmFile>
uploadCrmFile(data)            → CrmFile
deleteCrmFile(id)              → void
downloadCrmFile(id)            → Blob
getCrmFilesForEntity(type, id) → CrmFile[]

// Pipeline Stages
listCrmPipelineStages()        → PaginatedResponse<PipelineStage>
createCrmPipelineStage(data)   → PipelineStage
updateCrmPipelineStage(id, data) → PipelineStage
deleteCrmPipelineStage(id)     → void
reorderCrmPipelineStages(data) → void

// Reports
getCrmReportPipeline()         → object
getCrmReportConversion()       → object
getCrmReportActivity()         → object
getCrmReportLeadsBySource()    → object
getCrmReportMonthlyTrends()    → object
getCrmReportOverview()         → object

// Search
searchCrm(query)               → SearchResult[]

// Dashboard
getCrmDashboard()              → DashboardStats
```

---

## SimpleCRUDPage Integration

`SimpleCRUDPage<T>` (`components/simple-crud-page.tsx`) is the generic CRUD component used by leads, contacts, companies, opportunities, and activities.

### Required Config Props

```ts
{
  titleKey: string;           // i18n key
  titleFallback: string;      // Display fallback
  subtitleKey: string;
  subtitleFallback: string;
  createLabelKey: string;
  createLabelFallback: string;
  moduleKey: string;          // 'crm'
  dashboardHref: string;      // '/dashboard/modules/crm'
  serverSide: true;

  fields: FormField[];        // Form field definitions
  columns: () => ColumnDef<T>[];

  listFn: (params?) => Promise<PaginatedResponse<T>>;
  createFn: (data) => Promise<T>;
  updateFn: (id, data) => Promise<T>;
  deleteFn: (id) => Promise<void>;

  toForm?: (row: T) => Record<string, string>;   // Row → form values
  fromForm?: (form) => Partial<T>;               // Form values → API payload
}
```

### FormField Type

```ts
{
  name: string;
  label: string;
  type?: 'text' | 'email' | 'phone' | 'url' | 'number' | 'textarea' | 'select';
  required?: boolean;
  options?: { value: string; label: string }[];  // for 'select' type
}
```

---

## Component Dependencies

| Component / Utility | Source | Used In |
|---|---|---|
| `SimpleCRUDPage` | `@/components/simple-crud-page` | leads, contacts, companies, opportunities, activities |
| `ModulePageHeader` | `@/components/module-page-header` | deals, pipeline |
| `Card`, `CardContent` | `@/components/ui/card` | deals |
| `Badge` | `@/components/ui/badge` | pipeline, leads (status), opportunities (stage) |
| `Skeleton` | `@/components/ui/skeleton` | pipeline loading state |
| `toast` (sonner) | `sonner` | all pages — error/success feedback |
| `ColumnDef` | `@tanstack/react-table` | all list pages |
| Lucide icons | `lucide-react` | deals (`DollarSign`, `TrendingUp`, `Handshake`, `Loader2`), pipeline, leads |

---

## State Management Patterns

### CRUD Pages
State is entirely managed inside `SimpleCRUDPage` — no local state needed in the page component.

### Deals Kanban
```
useState<KanbanColumn[]>  ← full board state
useRef<{ id, fromStage }> ← drag reference (avoids re-render during drag)
```

Optimistic update pattern:
```ts
setColumns(prev => prev.map(col => {
  if (col.stage === fromStage) remove card
  if (col.stage === toStage)   add card
  return col unchanged otherwise
}))
// then call API; on failure: reload from server
```

### Pipeline Page
```
useState<PipelineStage[]>  ← loaded once on mount
useState<boolean>          ← loading flag
```

Data fetching via `useEffect` + async `.then().catch().finally()` chain (avoids ESLint `setState-in-effect` false positive):
```ts
useEffect(() => {
  getCrmPipeline()
    .then(data => setStages(data ?? []))
    .catch(() => toast.error('...'))
    .finally(() => setLoading(false));
}, []);
```

---

## TypeScript Interfaces

```ts
interface Lead {
  id: number;
  name: string;
  email: string | null;
  phone: string | null;
  status: 'new' | 'contacted' | 'qualified' | 'converted' | 'lost';
  source: string | null;
  value: number | null;
  company_id: number | null;
}

interface Opportunity {
  id: number;
  name: string;
  stage: 'prospecting' | 'qualification' | 'proposal' | 'negotiation' | 'closed_won' | 'closed_lost';
  expected_revenue: number | null;
  probability: number | null;
  expected_close_date: string | null;
  contact?: { name: string } | null;
  assignedUser?: { name: string } | null;
}

interface Contact {
  id: number;
  first_name: string;
  last_name: string;
  email: string | null;
  phone: string | null;
  title: string | null;
  company_id: number | null;
}

interface Company {
  id: number;
  name: string;
  email: string | null;
  phone: string | null;
  website: string | null;
  industry: string | null;
  type: string | null;
}

interface Activity {
  id: number;
  subject: string;
  type: 'call' | 'email' | 'meeting' | 'task' | 'note';
  status: string;
  due_date: string | null;
  assigned_to: number | null;
}

interface KanbanColumn {
  stage: string;
  label: string;
  color: string;
  probability: number;
  count: number;
  value: number;
  opportunities: Deal[];
}
```
