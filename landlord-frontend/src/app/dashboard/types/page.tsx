"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listTypes, createType, updateType, deleteType, type TypeRow } from "@/lib/resources";

const config: SimpleCRUDConfig<TypeRow> = {
  titleKey: "dashboard.types.title",
  titleFallback: "Types",
  subtitleKey: "dashboard.types.subtitle",
  subtitleFallback: "Manage content types.",
  createLabelKey: "dashboard.types.create",
  createLabelFallback: "Add Type",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "name" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "status", label: "Status", type: "select", options: [{ value: "active", label: "Active" }, { value: "inactive", label: "Inactive" }] },
    { name: "icon", label: "Icon", type: "file", accept: "image/*" },
    { name: "priority", label: "Priority", type: "number", placeholder: "0" },
  ],
  listFn: listTypes,
  createFn: createType,
  updateFn: updateType,
  deleteFn: deleteType,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.types.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug, description: row.description ?? "", status: row.status ?? "active", icon: row.icon ?? "", priority: row.priority ? String(row.priority) : "0" }),
  fromForm: (form) => ({ ...form, priority: Number(form.priority) }),
};

export default function TypesPage() {
  return <SimpleCRUDPage config={config} />;
}
