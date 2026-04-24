"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listCurrencies, createCurrency, updateCurrency, deleteCurrency, type CurrencyRow } from "@/lib/resources";

const config: SimpleCRUDConfig<CurrencyRow> = {
  titleKey: "dashboard.currencies.title",
  titleFallback: "Currencies",
  subtitleKey: "dashboard.currencies.subtitle",
  subtitleFallback: "Manage supported currencies.",
  createLabelKey: "dashboard.currencies.create",
  createLabelFallback: "Add Currency",
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
  toForm: (row) => ({ name: row.name, code: row.code, symbol: row.symbol, is_active: row.is_active ? "1" : "0" }),
  fromForm: (form) => ({ ...form, is_active: form.is_active === "1" }),
};

export default function CurrenciesPage() {
  return <SimpleCRUDPage config={config} />;
}
