"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listBudgets, createBudget, updateBudget, deleteBudget } from "@/lib/accounting-resources";

type Budget = { id: number; name: string; fiscal_year_id: number; status: string; total_amount: number; start_date: string; end_date: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Budget>[] => [
  { accessorKey: "name", header: t("accounting.name", "Name"), meta: { searchable: true } },
  { accessorKey: "status", header: t("accounting.status", "Status") },
  { accessorKey: "total_amount", header: t("accounting.total", "Total Amount") },
  { accessorKey: "start_date", header: t("accounting.start_date", "Start Date") },
  { accessorKey: "end_date", header: t("accounting.end_date", "End Date") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "fiscal_year_id", label: "Fiscal Year ID", type: "number", required: true },
  { name: "status", label: "Status", type: "select", options: [
    { value: "draft", label: "Draft" }, { value: "active", label: "Active" }, { value: "archived", label: "Archived" },
  ]},
  { name: "total_amount", label: "Total Amount", type: "number" },
  { name: "start_date", label: "Start Date" },
  { name: "end_date", label: "End Date" },
  { name: "description", label: "Description", type: "textarea" },
];

export default function BudgetsPage() {
  return (
    <SimpleCRUDPage<Budget>
      config={{
        titleKey: "accounting.budgets",
        titleFallback: "Budgets",
        subtitleKey: "accounting.budgets_subtitle",
        subtitleFallback: "Manage your budgets",
        createLabelKey: "accounting.add_budget",
        createLabelFallback: "Add Budget",
        moduleKey: "accounting",
        dashboardHref: "/dashboard/modules/accounting",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listBudgets<Budget>(params),
        createFn: createBudget,
        updateFn: updateBudget,
        deleteFn: deleteBudget,
        toForm: (row) => ({
          name: row.name ?? "", fiscal_year_id: row.fiscal_year_id != null ? String(row.fiscal_year_id) : "",
          status: row.status ?? "draft", total_amount: row.total_amount != null ? String(row.total_amount) : "",
        }),
        fromForm: (form) => ({
          name: form.name, fiscal_year_id: form.fiscal_year_id ? parseInt(form.fiscal_year_id) : undefined,
          status: form.status || undefined, total_amount: form.total_amount ? parseFloat(form.total_amount) : undefined,
        }),
      }}
    />
  );
}
