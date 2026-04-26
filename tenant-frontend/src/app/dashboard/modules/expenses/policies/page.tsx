"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listExpensePolicies, createExpensePolicy, updateExpensePolicy, deleteExpensePolicy } from "@/lib/expenses-resources";

type Policy = { id: number; name: string; type: string; is_active: boolean; priority: number };

const columns = (t: (k: string, f: string) => string): ColumnDef<Policy>[] => [
  { accessorKey: "name", header: t("expenses.name", "Name"), meta: { searchable: true } },
  { accessorKey: "type", header: t("expenses.type", "Type") },
  { accessorKey: "is_active", header: t("expenses.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
  { accessorKey: "priority", header: t("expenses.priority", "Priority") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "type", label: "Type", type: "select", required: true, options: [
    { value: "max_amount", label: "Max Amount" }, { value: "receipt_required", label: "Receipt Required" },
    { value: "auto_approval", label: "Auto Approval" }, { value: "category_restriction", label: "Category Restriction" },
    { value: "duplicate_check", label: "Duplicate Check" },
  ]},
  { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  { name: "priority", label: "Priority", type: "number" },
  { name: "description", label: "Description", type: "textarea" },
];

export default function ExpensePoliciesPage() {
  return (
    <SimpleCRUDPage<Policy>
      config={{
        titleKey: "expenses.policies",
        titleFallback: "Expense Policies",
        subtitleKey: "expenses.policies_subtitle",
        subtitleFallback: "Manage expense policies",
        createLabelKey: "expenses.add_policy",
        createLabelFallback: "Add Policy",
        moduleKey: "expenses",
        dashboardHref: "/dashboard/modules/expenses",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listExpensePolicies<Policy>(params),
        createFn: createExpensePolicy,
        updateFn: updateExpensePolicy,
        deleteFn: deleteExpensePolicy,
        toForm: (row) => ({
          name: row.name ?? "", type: row.type ?? "max_amount",
          is_active: row.is_active ? "1" : "0", priority: row.priority != null ? String(row.priority) : "0",
        }),
        fromForm: (form) => ({
          name: form.name, type: form.type, is_active: form.is_active === "1",
          priority: form.priority ? parseInt(form.priority) : 0,
        }),
      }}
    />
  );
}
