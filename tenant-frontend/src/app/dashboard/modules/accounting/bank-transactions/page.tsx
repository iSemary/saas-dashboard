"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listBankTransactions, createBankTransaction, updateBankTransaction, deleteBankTransaction } from "@/lib/accounting-resources";

type BankTransaction = { id: number; bank_account_id: number; type: string; amount: number; date: string; reference: string; description: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<BankTransaction>[] => [
  { accessorKey: "date", header: t("accounting.date", "Date") },
  { accessorKey: "type", header: t("accounting.type", "Type") },
  { accessorKey: "amount", header: t("accounting.amount", "Amount") },
  { accessorKey: "reference", header: t("accounting.reference", "Reference"), meta: { searchable: true } },
  { accessorKey: "description", header: t("accounting.description", "Description") },
];

const fields: FieldDef[] = [
  { name: "bank_account_id", label: "Bank Account ID", type: "number", required: true },
  { name: "type", label: "Type", type: "select", required: true, options: [
    { value: "deposit", label: "Deposit" }, { value: "withdrawal", label: "Withdrawal" }, { value: "transfer", label: "Transfer" },
  ]},
  { name: "amount", label: "Amount", type: "number", required: true },
  { name: "date", label: "Date", required: true },
  { name: "reference", label: "Reference" },
  { name: "description", label: "Description", type: "textarea" },
];

export default function BankTransactionsPage() {
  return (
    <SimpleCRUDPage<BankTransaction>
      config={{
        titleKey: "accounting.bank_transactions",
        titleFallback: "Bank Transactions",
        subtitleKey: "accounting.bank_transactions_subtitle",
        subtitleFallback: "Manage bank transactions",
        createLabelKey: "accounting.add_transaction",
        createLabelFallback: "Add Transaction",
        moduleKey: "accounting",
        dashboardHref: "/dashboard/modules/accounting",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listBankTransactions<BankTransaction>(params),
        createFn: createBankTransaction,
        updateFn: updateBankTransaction,
        deleteFn: deleteBankTransaction,
        toForm: (row) => ({
          bank_account_id: row.bank_account_id != null ? String(row.bank_account_id) : "",
          type: row.type ?? "deposit", amount: row.amount != null ? String(row.amount) : "",
          date: row.date ?? "", reference: row.reference ?? "",
        }),
        fromForm: (form) => ({
          bank_account_id: form.bank_account_id ? parseInt(form.bank_account_id) : undefined,
          type: form.type, amount: form.amount ? parseFloat(form.amount) : 0,
          date: form.date, reference: form.reference || undefined,
        }),
      }}
    />
  );
}
