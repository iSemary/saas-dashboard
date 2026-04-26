"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listExpenses, createExpense, updateExpense, deleteExpense } from "@/lib/expenses-resources";

type Expense = { id: number; title: string; amount: number; currency: string; date: string; status: string; vendor: string; category_id: number };

const columns = (t: (k: string, f: string) => string): ColumnDef<Expense>[] => [
  { accessorKey: "title", header: t("expenses.title", "Title"), meta: { searchable: true } },
  { accessorKey: "amount", header: t("expenses.amount", "Amount") },
  { accessorKey: "date", header: t("expenses.date", "Date") },
  { accessorKey: "status", header: t("expenses.status", "Status") },
  { accessorKey: "vendor", header: t("expenses.vendor", "Vendor") },
];

const fields: FieldDef[] = [
  { name: "title", label: "Title", required: true },
  { name: "amount", label: "Amount", type: "number", required: true },
  { name: "date", label: "Date", required: true },
  { name: "category_id", label: "Category ID", type: "number", required: true },
  { name: "currency", label: "Currency" },
  { name: "vendor", label: "Vendor" },
  { name: "reference", label: "Reference" },
  { name: "is_billable", label: "Billable", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  { name: "description", label: "Description", type: "textarea" },
];

export default function ExpensesPage() {
  return (
    <SimpleCRUDPage<Expense>
      config={{
        titleKey: "expenses.expenses",
        titleFallback: "Expenses",
        subtitleKey: "expenses.expenses_subtitle",
        subtitleFallback: "Manage your expenses",
        createLabelKey: "expenses.add_expense",
        createLabelFallback: "Add Expense",
        moduleKey: "expenses",
        dashboardHref: "/dashboard/modules/expenses",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listExpenses<Expense>(params),
        createFn: createExpense,
        updateFn: updateExpense,
        deleteFn: deleteExpense,
        toForm: (row) => ({
          title: row.title ?? "", amount: row.amount != null ? String(row.amount) : "",
          date: row.date ?? "", category_id: row.category_id != null ? String(row.category_id) : "",
          currency: row.currency ?? "USD", vendor: row.vendor ?? "",
          is_billable: "0",
        }),
        fromForm: (form) => ({
          title: form.title, amount: form.amount ? parseFloat(form.amount) : 0,
          date: form.date, category_id: form.category_id ? parseInt(form.category_id) : undefined,
          currency: form.currency || "USD", vendor: form.vendor || undefined,
          is_billable: form.is_billable === "1",
        }),
      }}
    />
  );
}
