"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listExpenseReports, createExpenseReport, updateExpenseReport, deleteExpenseReport } from "@/lib/expenses-resources";

type ExpenseReport = { id: number; title: string; status: string; total_amount: number };

const columns = (t: (k: string, f: string) => string): ColumnDef<ExpenseReport>[] => [
  { accessorKey: "title", header: t("expenses.title", "Title"), meta: { searchable: true } },
  { accessorKey: "status", header: t("expenses.status", "Status") },
  { accessorKey: "total_amount", header: t("expenses.total", "Total") },
];

const fields: FieldDef[] = [
  { name: "title", label: "Title", required: true },
  { name: "description", label: "Description", type: "textarea" },
];

export default function ExpenseReportsPage() {
  return (
    <SimpleCRUDPage<ExpenseReport>
      config={{
        titleKey: "expenses.reports",
        titleFallback: "Expense Reports",
        subtitleKey: "expenses.reports_subtitle",
        subtitleFallback: "Manage expense reports",
        createLabelKey: "expenses.add_report",
        createLabelFallback: "Add Report",
        moduleKey: "expenses",
        dashboardHref: "/dashboard/modules/expenses",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listExpenseReports<ExpenseReport>(params),
        createFn: createExpenseReport,
        updateFn: updateExpenseReport,
        deleteFn: deleteExpenseReport,
        toForm: (row) => ({ title: row.title ?? "" }),
        fromForm: (form) => ({ title: form.title }),
      }}
    />
  );
}
