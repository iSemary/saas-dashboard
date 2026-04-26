"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listExpenseTags, createExpenseTag, updateExpenseTag, deleteExpenseTag } from "@/lib/expenses-resources";

type Tag = { id: number; name: string; color: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Tag>[] => [
  { accessorKey: "name", header: t("expenses.name", "Name"), meta: { searchable: true } },
  { accessorKey: "color", header: t("expenses.color", "Color") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "color", label: "Color (hex)" },
];

export default function ExpenseTagsPage() {
  return (
    <SimpleCRUDPage<Tag>
      config={{
        titleKey: "expenses.tags",
        titleFallback: "Expense Tags",
        subtitleKey: "expenses.tags_subtitle",
        subtitleFallback: "Manage expense tags",
        createLabelKey: "expenses.add_tag",
        createLabelFallback: "Add Tag",
        moduleKey: "expenses",
        dashboardHref: "/dashboard/modules/expenses",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listExpenseTags<Tag>(params),
        createFn: createExpenseTag,
        updateFn: updateExpenseTag,
        deleteFn: deleteExpenseTag,
        toForm: (row) => ({ name: row.name ?? "", color: row.color ?? "" }),
        fromForm: (form) => ({ name: form.name, color: form.color || undefined }),
      }}
    />
  );
}
