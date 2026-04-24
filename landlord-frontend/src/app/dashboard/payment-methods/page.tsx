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
    { name: "slug", label: "Slug" },
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
  toForm: (row) => ({ name: row.name, slug: row.slug, is_active: row.is_active ? "1" : "0" }),
  fromForm: (form) => ({ ...form, is_active: form.is_active === "1" }),
};

export default function PaymentMethodsPage() {
  return <SimpleCRUDPage config={config} />;
}
