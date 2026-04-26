"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listFiscalYears, createFiscalYear, updateFiscalYear, deleteFiscalYear } from "@/lib/accounting-resources";

type FiscalYear = { id: number; name: string; start_date: string; end_date: string; is_active: boolean; is_closed: boolean };

const columns = (t: (k: string, f: string) => string): ColumnDef<FiscalYear>[] => [
  { accessorKey: "name", header: t("accounting.name", "Name"), meta: { searchable: true } },
  { accessorKey: "start_date", header: t("accounting.start_date", "Start Date") },
  { accessorKey: "end_date", header: t("accounting.end_date", "End Date") },
  { accessorKey: "is_active", header: t("accounting.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "start_date", label: "Start Date", required: true },
  { name: "end_date", label: "End Date", required: true },
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "description", label: "Description", type: "textarea" },
];

export default function FiscalYearsPage() {
  return (
    <SimpleCRUDPage<FiscalYear>
      config={{
        titleKey: "accounting.fiscal_years",
        titleFallback: "Fiscal Years",
        subtitleKey: "accounting.fiscal_years_subtitle",
        subtitleFallback: "Manage your fiscal years",
        createLabelKey: "accounting.add_fiscal_year",
        createLabelFallback: "Add Fiscal Year",
        moduleKey: "accounting",
        dashboardHref: "/dashboard/modules/accounting",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listFiscalYears<FiscalYear>(params),
        createFn: createFiscalYear,
        updateFn: updateFiscalYear,
        deleteFn: deleteFiscalYear,
        toForm: (row) => ({
          name: row.name ?? "", start_date: row.start_date ?? "", end_date: row.end_date ?? "",
          is_active: row.is_active ? "1" : "0",
        }),
        fromForm: (form) => ({
          name: form.name, start_date: form.start_date, end_date: form.end_date,
          is_active: form.is_active === "1",
        }),
      }}
    />
  );
}
