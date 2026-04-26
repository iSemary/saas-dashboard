"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listExpenseCategories, createExpenseCategory, updateExpenseCategory, deleteExpenseCategory } from "@/lib/expenses-resources";

type Category = { id: number; name: string; is_active: boolean; requires_receipt: boolean; max_amount: number | null };

const columns = (t: (k: string, f: string) => string): ColumnDef<Category>[] => [
  { accessorKey: "name", header: t("expenses.name", "Name"), meta: { searchable: true } },
  { accessorKey: "is_active", header: t("expenses.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
  { accessorKey: "requires_receipt", header: t("expenses.receipt", "Receipt"), cell: ({ row }) => row.original.requires_receipt ? "Required" : "Optional" },
  { accessorKey: "max_amount", header: t("expenses.max_amount", "Max Amount") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  { name: "requires_receipt", label: "Receipt Required", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  { name: "max_amount", label: "Max Amount", type: "number" },
  { name: "description", label: "Description", type: "textarea" },
];

export default function ExpenseCategoriesPage() {
  return (
    <SimpleCRUDPage<Category>
      config={{
        titleKey: "expenses.categories",
        titleFallback: "Expense Categories",
        subtitleKey: "expenses.categories_subtitle",
        subtitleFallback: "Manage expense categories",
        createLabelKey: "expenses.add_category",
        createLabelFallback: "Add Category",
        moduleKey: "expenses",
        dashboardHref: "/dashboard/modules/expenses",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listExpenseCategories<Category>(params),
        createFn: createExpenseCategory,
        updateFn: updateExpenseCategory,
        deleteFn: deleteExpenseCategory,
        toForm: (row) => ({
          name: row.name ?? "", is_active: row.is_active ? "1" : "0",
          requires_receipt: row.requires_receipt ? "1" : "0",
          max_amount: row.max_amount != null ? String(row.max_amount) : "",
        }),
        fromForm: (form) => ({
          name: form.name, is_active: form.is_active === "1",
          requires_receipt: form.requires_receipt === "1",
          max_amount: form.max_amount ? parseFloat(form.max_amount) : undefined,
        }),
      }}
    />
  );
}
