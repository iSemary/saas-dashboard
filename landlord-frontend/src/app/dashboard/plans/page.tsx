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
    { name: "slug", label: "Slug" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "price", label: "Price", type: "number", required: true },
    { name: "currency", label: "Currency", placeholder: "USD", required: true },
    { name: "billing_period", label: "Billing Period", type: "select", options: [{ value: "monthly", label: "Monthly" }, { value: "yearly", label: "Yearly" }, { value: "lifetime", label: "Lifetime" }] },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listPlans,
  createFn: createPlan,
  updateFn: updatePlan,
  deleteFn: deletePlan,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "price", header: t("dashboard.plans.price", "Price") },
    { accessorKey: "currency", header: t("dashboard.plans.currency", "Currency") },
    { accessorKey: "billing_period", header: t("dashboard.plans.billing_period", "Billing") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug, description: row.description ?? "", price: String(row.price), currency: row.currency, billing_period: row.billing_period, is_active: row.is_active ? "1" : "0" }),
  fromForm: (form) => ({ ...form, price: Number(form.price), is_active: form.is_active === "1" }),
};

export default function PlansPage() {
  return <SimpleCRUDPage config={config} />;
}
