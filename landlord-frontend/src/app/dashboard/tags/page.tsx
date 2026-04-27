"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { Upload } from "lucide-react";
import { listTags, createTag, deleteTag, type TagRow } from "@/lib/resources";

const config: SimpleCRUDConfig<TagRow> = {
  titleKey: "dashboard.tags.title",
  titleFallback: "Tags",
  subtitleKey: "dashboard.tags.subtitle",
  subtitleFallback: "Manage tags for content classification.",
  createLabelKey: "dashboard.tags.create",
  createLabelFallback: "Add Tag",
  actionButtons: [
    {
      labelKey: "dashboard.import",
      labelFallback: "Import",
      icon: Upload,
      variant: "outline",
      href: "/dashboard/import/tags",
    },
  ],
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "name" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "icon", label: "Icon", type: "file", accept: "image/*" },
    { name: "priority", label: "Priority", type: "number", placeholder: "0" },
    { name: "status", label: "Status", type: "select", options: [{ value: "active", label: "Active" }, { value: "inactive", label: "Inactive" }] },
  ],
  listFn: listTags,
  createFn: createTag,
  deleteFn: deleteTag,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.tags.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug, description: row.description ?? "", icon: row.icon ?? "", priority: row.priority ? String(row.priority) : "0", status: row.status ?? "active" }),
  fromForm: (form) => ({ ...form, priority: Number(form.priority) }),
};

export default function TagsPage() {
  return <SimpleCRUDPage config={config} />;
}
