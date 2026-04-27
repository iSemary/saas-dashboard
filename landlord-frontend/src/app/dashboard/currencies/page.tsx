"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { Upload } from "lucide-react";
import { listCurrencies, createCurrency, updateCurrency, deleteCurrency, type CurrencyRow } from "@/lib/resources";

const config: SimpleCRUDConfig<CurrencyRow> = {
  titleKey: "dashboard.currencies.title",
  titleFallback: "Currencies",
  subtitleKey: "dashboard.currencies.subtitle",
  subtitleFallback: "Manage supported currencies.",
  createLabelKey: "dashboard.currencies.create",
  createLabelFallback: "Add Currency",
  actionButtons: [
    {
      labelKey: "dashboard.import",
      labelFallback: "Import",
      icon: Upload,
      variant: "outline",
      href: "/dashboard/import/currencies",
    },
  ],
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "code", label: "Code", placeholder: "USD", required: true },
    { name: "symbol", label: "Symbol", placeholder: "$", required: true },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listCurrencies,
  createFn: createCurrency,
  updateFn: updateCurrency,
  deleteFn: deleteCurrency,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "code", header: t("dashboard.currencies.code", "Code") },
    { accessorKey: "symbol", header: t("dashboard.currencies.symbol", "Symbol") },
  ],
  toForm: (row) => ({ name: row.name, code: row.code, symbol: row.symbol, decimal_places: row.decimal_places ? String(row.decimal_places) : "2", exchange_rate: row.exchange_rate ? String(row.exchange_rate) : "", exchange_rate_last_updated: row.exchange_rate_last_updated ?? "", symbol_position: row.symbol_position ?? "after", base_currency: row.base_currency ? "1" : "0", priority: row.priority ? String(row.priority) : "0", note: row.note ?? "", status: row.status ?? "active" }),
  fromForm: (form) => ({ ...form, decimal_places: Number(form.decimal_places), exchange_rate: form.exchange_rate ? Number(form.exchange_rate) : null, base_currency: form.base_currency === "1", priority: Number(form.priority) }),
};

export default function CurrenciesPage() {
  return <SimpleCRUDPage config={config} />;
}
