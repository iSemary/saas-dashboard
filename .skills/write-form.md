# Write Form Skill

Create and modify forms in the landlord and tenant frontends.

## SimpleCRUDPage Forms

For simple CRUD operations, use the `SimpleCRUDPage` component with a `SimpleCRUDConfig`.

### Basic Structure

```tsx
"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listItems, createItem, updateItem, deleteItem, type ItemRow } from "@/lib/resources";

const config: SimpleCRUDConfig<ItemRow> = {
  titleKey: "dashboard.items.title",
  titleFallback: "Items",
  subtitleKey: "dashboard.items.subtitle",
  subtitleFallback: "Manage items.",
  createLabelKey: "dashboard.items.create",
  createLabelFallback: "Add Item",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "name" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listItems,
  createFn: createItem,
  updateFn: updateItem,
  deleteFn: deleteItem,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
  ],
  toForm: (row) => ({ 
    name: row.name, 
    slug: row.slug ?? "", 
    description: row.description ?? "",
    is_active: row.is_active ? "1" : "0" 
  }),
  fromForm: (form) => ({ 
    ...form, 
    is_active: form.is_active === "1" 
  }),
};

export default function ItemsPage() {
  return <SimpleCRUDPage config={config} />;
}
```

### Field Types

| Type | Description | Additional Props |
|------|-------------|------------------|
| `text` | Standard text input | `placeholder`, `required` |
| `email` | Email input | `placeholder`, `required` |
| `password` | Password input | `placeholder`, `required` |
| `number` | Number input | `placeholder`, `required` |
| `url` | URL input | `placeholder`, `required` |
| `textarea` | Multi-line text | `placeholder` |
| `richtext` | Rich text editor | `placeholder` |
| `select` | Dropdown select | `options: [{ value, label }]`, `required` |
| `slug` | Auto-generated slug | `sourceField` (field to base slug on) |
| `entity` | Entity selector dropdown | `listFn`, `optionLabelKey`, `optionValueKey`, `parentKey` |

### Entity Selector Fields

Use `type: "entity"` to replace ID number inputs with searchable dropdowns showing entity names.

```tsx
// Simple entity selector
{ 
  name: "brand_id", 
  label: "Brand", 
  type: "entity", 
  listFn: listBrands, 
  optionLabelKey: "name", 
  optionValueKey: "id" 
}

// With parent context (shows "Name, ParentName")
{ 
  name: "province_id", 
  label: "Province", 
  type: "entity", 
  listFn: listProvinces, 
  optionLabelKey: "name", 
  optionValueKey: "id",
  parentKey: "country",
  required: true 
}
```

### Form Data Conversion

Always convert between form strings and API types:

```tsx
toForm: (row) => ({ 
  name: row.name,
  brand_id: row.brand_id ? String(row.brand_id) : "", // number -> string
  is_active: row.is_active ? "1" : "0", // boolean -> "1"/"0"
}),

fromForm: (form) => ({ 
  name: form.name,
  brand_id: form.brand_id ? Number(form.brand_id) : null, // string -> number
  is_active: form.is_active === "1", // "1"/"0" -> boolean
}),
```

## Custom Forms

For complex forms not using SimpleCRUDPage, use the Sheet component with manual form state:

```tsx
"use client";

import { useState } from "react";
import { Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription, SheetFooter } from "@/components/ui/sheet";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Button } from "@/components/ui/button";
import { EntitySelector } from "@/components/entity-selector";

export function MyForm() {
  const [open, setOpen] = useState(false);
  const [form, setForm] = useState({
    name: "",
    brand_id: "",
  });

  return (
    <Sheet open={open} onOpenChange={setOpen}>
      <SheetContent>
        <SheetHeader>
          <SheetTitle>Create Item</SheetTitle>
        </SheetHeader>
        <div className="space-y-4 py-4">
          <div className="space-y-2">
            <Label htmlFor="name">Name</Label>
            <Input 
              id="name" 
              value={form.name} 
              onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} 
            />
          </div>
          <div className="space-y-2">
            <Label>Brand</Label>
            <EntitySelector
              value={form.brand_id}
              onChange={(v) => setForm((f) => ({ ...f, brand_id: v }))}
              listFn={listBrands}
              optionLabelKey="name"
              optionValueKey="id"
              required
            />
          </div>
        </div>
        <SheetFooter>
          <Button onClick={() => setOpen(false)}>Cancel</Button>
          <Button onClick={handleSave}>Save</Button>
        </SheetFooter>
      </SheetContent>
    </Sheet>
  );
}
```

## Key Principles

1. **Use EntitySelector for all `*_id` fields** - Never use raw number inputs for entity IDs
2. **Always convert types in `toForm` and `fromForm`** - Forms use strings, APIs use proper types
3. **Add `required` prop for mandatory fields** - Shows validation and visual indicators
4. **Use `parentKey` for hierarchical data** - Shows context like "City, Province, Country"
5. **Keep labels user-friendly** - Use "Brand" not "Brand ID", "Province" not "Province ID"
