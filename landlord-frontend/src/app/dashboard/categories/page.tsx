"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listCategories, createCategory, updateCategory, deleteCategory, type CategoryRow } from "@/lib/resources";

const config: SimpleCRUDConfig<CategoryRow> = {
  titleKey: "dashboard.categories.title",
  titleFallback: "Categories",
  subtitleKey: "dashboard.categories.subtitle",
  subtitleFallback: "Manage content categories.",
  createLabelKey: "dashboard.categories.create",
  createLabelFallback: "Add Category",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug" },
    { name: "parent_id", label: "Parent ID", type: "number", placeholder: "0 for root" },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
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
  toForm: (row) => ({ name: row.name, slug: row.slug, parent_id: row.parent_id ? String(row.parent_id) : "", is_active: row.is_active ? "1" : "0" }),
  fromForm: (form) => ({ ...form, parent_id: form.parent_id ? Number(form.parent_id) : null, is_active: form.is_active === "1" }),
};

export default function CategoriesPage() {
  return <SimpleCRUDPage config={config} />;
}
