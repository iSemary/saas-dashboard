# Add "Open Dashboard" Button to Module Page Headers

Add an "Open Dashboard" button next to the module title in the header section of all module pages, navigating to the module's root dashboard page, with RTL/LTR-aware layout.

## Scope

**8 pages** share the same header pattern (`<div className="flex items-center gap-2">` with icon + `<h1>`):

- **POS**: `modules/pos/page.tsx`, `modules/pos/products/page.tsx`, `modules/pos/orders/page.tsx`
- **CRM**: `modules/crm/page.tsx`, `modules/crm/companies/page.tsx`, `modules/crm/contacts/page.tsx`, `modules/crm/deals/page.tsx`
- **HR**: `modules/hr/page.tsx`, `modules/hr/departments/page.tsx`, `modules/hr/employees/page.tsx`, `modules/hr/leave-requests/page.tsx`

## Approach: Shared `ModulePageHeader` Component

Create a reusable component to avoid duplicating the header + button logic across 10 files.

### 1. Create `ModulePageHeader` component

**File**: `tenant-frontend/src/components/module-page-header.tsx`

```tsx
Props:
  - icon: LucideIcon
  - titleKey: string          // i18n key
  - titleFallback: string     // e.g. "POS Module"
  - subtitleKey: string
  - subtitleFallback: string
  - dashboardHref: string     // e.g. "/dashboard/modules/pos"
```

Renders:
- The existing header structure (rounded-xl border bg-muted/40 p-4)
- `flex items-center justify-between` (instead of just `flex items-center gap-2`)
- Left side: icon + title + subtitle (existing)
- Right side: `<Button variant="outline" size="sm">` with `LayoutDashboard` icon + "Open Dashboard" label
- Uses `useI18n()` for `dir` and `t()`
- Button uses `router.push(dashboardHref)` on click

RTL handling: `justify-between` naturally works for both LTR (button on right) and RTL (button on left) since the browser reverses flex direction. No extra RTL CSS needed.

### 2. Update all 10 module pages

Replace the inline header block with `<ModulePageHeader ... />` in each page.

### 3. i18n key

- Button label: `t("dashboard.modules.open_dashboard", "Open Dashboard")`

## Files to Create
- `tenant-frontend/src/components/module-page-header.tsx`

## Files to Modify (10)
- `tenant-frontend/src/app/dashboard/modules/pos/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/pos/products/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/pos/orders/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/crm/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/crm/companies/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/crm/contacts/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/crm/deals/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/hr/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/hr/departments/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/hr/employees/page.tsx`
- `tenant-frontend/src/app/dashboard/modules/hr/leave-requests/page.tsx`
