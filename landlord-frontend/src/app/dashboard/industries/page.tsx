"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listIndustries, createIndustry, updateIndustry, deleteIndustry, type IndustryRow } from "@/lib/resources";

const config: SimpleCRUDConfig<IndustryRow> = {
  titleKey: "dashboard.industries.title",
  titleFallback: "Industries",
  subtitleKey: "dashboard.industries.subtitle",
  subtitleFallback: "Manage industry classifications.",
  createLabelKey: "dashboard.industries.create",
  createLabelFallback: "Add Industry",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "name" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "status", label: "Status", type: "select", options: [{ value: "active", label: "Active" }, { value: "inactive", label: "Inactive" }] },
    { name: "icon", label: "Icon", type: "file", accept: "image/*" },
    { name: "priority", label: "Priority", type: "number", placeholder: "0" },
  ],
  listFn: listIndustries,
  createFn: createIndustry,
  updateFn: updateIndustry,
  deleteFn: deleteIndustry,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.industries.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug, description: row.description ?? "", status: row.status ?? "active", icon: row.icon ?? "", priority: row.priority ? String(row.priority) : "0" }),
  fromForm: (form) => ({ ...form, priority: Number(form.priority) }),
};

export default function IndustriesPage() {
  return <SimpleCRUDPage config={config} />;
}
