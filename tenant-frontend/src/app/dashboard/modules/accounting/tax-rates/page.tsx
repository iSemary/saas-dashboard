"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listTaxRates, createTaxRate, updateTaxRate, deleteTaxRate } from "@/lib/accounting-resources";

type TaxRate = { id: number; name: string; rate: number; type: string; is_active: boolean };

const columns = (t: (k: string, f: string) => string): ColumnDef<TaxRate>[] => [
  { accessorKey: "name", header: t("accounting.name", "Name"), meta: { searchable: true } },
  { accessorKey: "rate", header: t("accounting.rate", "Rate %") },
  { accessorKey: "type", header: t("accounting.type", "Type") },
  { accessorKey: "is_active", header: t("accounting.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "rate", label: "Rate %", type: "number", required: true },
  { name: "type", label: "Type", type: "select", options: [
    { value: "sales", label: "Sales" }, { value: "purchase", label: "Purchase" }, { value: "withholding", label: "Withholding" },
  ]},
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "description", label: "Description", type: "textarea" },
];

export default function TaxRatesPage() {
  return (
    <SimpleCRUDPage<TaxRate>
      config={{
        titleKey: "accounting.tax_rates",
        titleFallback: "Tax Rates",
        subtitleKey: "accounting.tax_rates_subtitle",
        subtitleFallback: "Manage your tax rates",
        createLabelKey: "accounting.add_tax_rate",
        createLabelFallback: "Add Tax Rate",
        moduleKey: "accounting",
        dashboardHref: "/dashboard/modules/accounting",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listTaxRates<TaxRate>(params),
        createFn: createTaxRate,
        updateFn: updateTaxRate,
        deleteFn: deleteTaxRate,
        toForm: (row) => ({
          name: row.name ?? "", rate: row.rate != null ? String(row.rate) : "",
          type: row.type ?? "sales", is_active: row.is_active ? "1" : "0",
        }),
        fromForm: (form) => ({
          name: form.name, rate: form.rate ? parseFloat(form.rate) : 0,
          type: form.type || undefined, is_active: form.is_active === "1",
        }),
      }}
    />
  );
}
