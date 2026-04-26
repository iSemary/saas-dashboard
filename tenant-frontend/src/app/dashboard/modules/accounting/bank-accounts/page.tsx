"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listBankAccounts, createBankAccount, updateBankAccount, deleteBankAccount } from "@/lib/accounting-resources";

type BankAccount = { id: number; name: string; bank_name: string; account_number: string; currency: string; current_balance: number; is_active: boolean };

const columns = (t: (k: string, f: string) => string): ColumnDef<BankAccount>[] => [
  { accessorKey: "name", header: t("accounting.name", "Name"), meta: { searchable: true } },
  { accessorKey: "bank_name", header: t("accounting.bank_name", "Bank") },
  { accessorKey: "account_number", header: t("accounting.account_number", "Account #") },
  { accessorKey: "currency", header: t("accounting.currency", "Currency") },
  { accessorKey: "current_balance", header: t("accounting.balance", "Balance") },
  { accessorKey: "is_active", header: t("accounting.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "bank_name", label: "Bank Name", required: true },
  { name: "account_number", label: "Account Number", required: true },
  { name: "currency", label: "Currency" },
  { name: "opening_balance", label: "Opening Balance", type: "number" },
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "description", label: "Description", type: "textarea" },
];

export default function BankAccountsPage() {
  return (
    <SimpleCRUDPage<BankAccount>
      config={{
        titleKey: "accounting.bank_accounts",
        titleFallback: "Bank Accounts",
        subtitleKey: "accounting.bank_accounts_subtitle",
        subtitleFallback: "Manage your bank accounts",
        createLabelKey: "accounting.add_bank_account",
        createLabelFallback: "Add Bank Account",
        moduleKey: "accounting",
        dashboardHref: "/dashboard/modules/accounting",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listBankAccounts<BankAccount>(params),
        createFn: createBankAccount,
        updateFn: updateBankAccount,
        deleteFn: deleteBankAccount,
        toForm: (row) => ({
          name: row.name ?? "", bank_name: row.bank_name ?? "", account_number: row.account_number ?? "",
          currency: row.currency ?? "USD", opening_balance: row.current_balance != null ? String(row.current_balance) : "",
          is_active: row.is_active ? "1" : "0",
        }),
        fromForm: (form) => ({
          name: form.name, bank_name: form.bank_name, account_number: form.account_number,
          currency: form.currency || "USD", opening_balance: form.opening_balance ? parseFloat(form.opening_balance) : 0,
          is_active: form.is_active === "1",
        }),
      }}
    />
  );
}
