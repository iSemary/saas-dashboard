"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { Upload } from "lucide-react";
import { listCategories, createCategory, updateCategory, deleteCategory, type CategoryRow } from "@/lib/resources";

const config: SimpleCRUDConfig<CategoryRow> = {
  titleKey: "dashboard.categories.title",
  titleFallback: "Categories",
  subtitleKey: "dashboard.categories.subtitle",
  subtitleFallback: "Manage content categories.",
  createLabelKey: "dashboard.categories.create",
  createLabelFallback: "Add Category",
  actionButtons: [
    {
      labelKey: "dashboard.import",
      labelFallback: "Import",
      icon: Upload,
      variant: "outline",
      href: "/dashboard/import/categories",
    },
  ],
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "parent_id", label: "Parent Category", type: "entity", listFn: listCategories, optionLabelKey: "name", optionValueKey: "id" },
    { name: "icon", label: "Icon", type: "file", accept: "image/*" },
    { name: "priority", label: "Priority", type: "number", placeholder: "0" },
    { name: "status", label: "Status", type: "select", options: [{ value: "active", label: "Active" }, { value: "inactive", label: "Inactive" }] },
  ],
  listFn: listCategories,
  createFn: createCategory,
  updateFn: updateCategory,
  deleteFn: deleteCategory,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.categories.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug, description: row.description ?? "", parent_id: row.parent_id ? String(row.parent_id) : "", icon: row.icon ?? "", priority: row.priority ? String(row.priority) : "0", status: row.status ?? "active" }),
  fromForm: (form) => ({ ...form, parent_id: form.parent_id ? Number(form.parent_id) : null, priority: Number(form.priority) }),
};

export default function CategoriesPage() {
  return <SimpleCRUDPage config={config} />;
}
