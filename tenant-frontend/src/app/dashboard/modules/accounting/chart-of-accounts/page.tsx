"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams, PaginatedResponse } from "@/lib/tenant-resources";
import { listChartOfAccounts, createChartOfAccount, updateChartOfAccount, deleteChartOfAccount } from "@/lib/accounting-resources";

type Account = { id: number; code: string; name: string; type: string; sub_type: string; is_active: boolean; current_balance: number };

const columns = (t: (k: string, f: string) => string): ColumnDef<Account>[] => [
  { accessorKey: "code", header: t("accounting.code", "Code"), meta: { searchable: true } },
  { accessorKey: "name", header: t("accounting.name", "Name"), meta: { searchable: true } },
  { accessorKey: "type", header: t("accounting.type", "Type") },
  { accessorKey: "sub_type", header: t("accounting.sub_type", "Sub Type") },
  { accessorKey: "current_balance", header: t("accounting.balance", "Balance") },
  { accessorKey: "is_active", header: t("accounting.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "code", label: "Code", required: true },
  { name: "name", label: "Name", required: true },
  { name: "type", label: "Type", type: "select", required: true, options: [
    { value: "asset", label: "Asset" }, { value: "liability", label: "Liability" },
    { value: "equity", label: "Equity" }, { value: "income", label: "Income" },
    { value: "expense", label: "Expense" },
  ]},
  { name: "sub_type", label: "Sub Type" },
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "opening_balance", label: "Opening Balance", type: "number" },
  { name: "description", label: "Description", type: "textarea" },
];

export default function ChartOfAccountsPage() {
  return (
    <SimpleCRUDPage<Account>
      config={{
        titleKey: "accounting.chart_of_accounts",
        titleFallback: "Chart of Accounts",
        subtitleKey: "accounting.chart_of_accounts_subtitle",
        subtitleFallback: "Manage your chart of accounts",
        createLabelKey: "accounting.add_account",
        createLabelFallback: "Add Account",
        moduleKey: "accounting",
        dashboardHref: "/dashboard/modules/accounting",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listChartOfAccounts<Account>(params),
        createFn: createChartOfAccount,
        updateFn: updateChartOfAccount,
        deleteFn: deleteChartOfAccount,
        toForm: (row) => ({
          code: row.code ?? "", name: row.name ?? "", type: row.type ?? "asset",
          sub_type: row.sub_type ?? "", is_active: row.is_active ? "1" : "0",
          opening_balance: row.current_balance != null ? String(row.current_balance) : "",
        }),
        fromForm: (form) => ({
          code: form.code, name: form.name, type: form.type,
          sub_type: form.sub_type || undefined,
          is_active: form.is_active === "1",
          opening_balance: form.opening_balance ? parseFloat(form.opening_balance) : 0,
        }),
      }}
    />
  );
}
