"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listPaymentMethods, createPaymentMethod, deletePaymentMethod, type PaymentMethodRow } from "@/lib/resources";

const config: SimpleCRUDConfig<PaymentMethodRow> = {
  titleKey: "dashboard.payment_methods.title",
  titleFallback: "Payment Methods",
  subtitleKey: "dashboard.payment_methods.subtitle",
  subtitleFallback: "Manage available payment methods.",
  createLabelKey: "dashboard.payment_methods.create",
  createLabelFallback: "Add Method",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "name" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "provider", label: "Provider", placeholder: "stripe" },
    { name: "provider_config", label: "Provider Config", type: "textarea", placeholder: "{}" },
    { name: "priority", label: "Priority", type: "number", placeholder: "0" },
    { name: "metadata", label: "Metadata", type: "textarea", placeholder: "{}" },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listPaymentMethods,
  createFn: createPaymentMethod,
  deleteFn: deletePaymentMethod,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.payment_methods.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug, description: row.description ?? "", provider: row.provider ?? "", provider_config: row.provider_config ?? "", priority: row.priority ? String(row.priority) : "0", metadata: row.metadata ?? "", is_active: row.is_active ? "1" : "0" }),
  fromForm: (form) => ({ ...form, priority: Number(form.priority), is_active: form.is_active === "1" }),
};

export default function PaymentMethodsPage() {
  return <SimpleCRUDPage config={config} />;
}
