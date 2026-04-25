"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listPlans, createPlan, updatePlan, deletePlan, type PlanRow } from "@/lib/resources";

const config: SimpleCRUDConfig<PlanRow> = {
  titleKey: "dashboard.plans.title",
  titleFallback: "Plans",
  subtitleKey: "dashboard.plans.subtitle",
  subtitleFallback: "Manage subscription plans for tenants.",
  createLabelKey: "dashboard.plans.create",
  createLabelFallback: "Add Plan",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "name" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "features_summary", label: "Features Summary", type: "textarea" },
    { name: "sort_order", label: "Sort Order", type: "number", placeholder: "0" },
    { name: "is_popular", label: "Popular", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
    { name: "is_custom", label: "Custom", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
    { name: "metadata", label: "Metadata", type: "textarea", placeholder: "{}" },
    { name: "status", label: "Status", type: "select", options: [{ value: "active", label: "Active" }, { value: "inactive", label: "Inactive" }] },
  ],
  listFn: listPlans,
  createFn: createPlan,
  updateFn: updatePlan,
  deleteFn: deletePlan,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.plans.slug", "Slug") },
    { accessorKey: "status", header: t("dashboard.plans.status", "Status") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug, description: row.description ?? "", features_summary: row.features_summary ?? "", sort_order: row.sort_order ? String(row.sort_order) : "0", is_popular: row.is_popular ? "1" : "0", is_custom: row.is_custom ? "1" : "0", metadata: row.metadata ?? "", status: row.status ?? "active" }),
  fromForm: (form) => ({ ...form, sort_order: Number(form.sort_order), is_popular: form.is_popular === "1", is_custom: form.is_custom === "1" }),
};

export default function PlansPage() {
  return <SimpleCRUDPage config={config} />;
}
