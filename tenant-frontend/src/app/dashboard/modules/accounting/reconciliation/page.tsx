"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listReconciliations, createReconciliation, updateReconciliation, deleteReconciliation } from "@/lib/accounting-resources";

type Reconciliation = { id: number; bank_account_id: number; statement_date: string; statement_balance: number; book_balance: number; difference: number; status: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Reconciliation>[] => [
  { accessorKey: "statement_date", header: t("accounting.statement_date", "Statement Date") },
  { accessorKey: "statement_balance", header: t("accounting.statement_balance", "Statement Balance") },
  { accessorKey: "book_balance", header: t("accounting.book_balance", "Book Balance") },
  { accessorKey: "difference", header: t("accounting.difference", "Difference") },
  { accessorKey: "status", header: t("accounting.status", "Status") },
];

const fields: FieldDef[] = [
  { name: "bank_account_id", label: "Bank Account ID", type: "number", required: true },
  { name: "statement_date", label: "Statement Date", required: true },
  { name: "statement_balance", label: "Statement Balance", type: "number", required: true },
  { name: "book_balance", label: "Book Balance", type: "number" },
  { name: "notes", label: "Notes", type: "textarea" },
];

export default function ReconciliationPage() {
  return (
    <SimpleCRUDPage<Reconciliation>
      config={{
        titleKey: "accounting.reconciliation",
        titleFallback: "Reconciliation",
        subtitleKey: "accounting.reconciliation_subtitle",
        subtitleFallback: "Reconcile bank statements with your books",
        createLabelKey: "accounting.add_reconciliation",
        createLabelFallback: "Add Reconciliation",
        moduleKey: "accounting",
        dashboardHref: "/dashboard/modules/accounting",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listReconciliations<Reconciliation>(params),
        createFn: createReconciliation,
        updateFn: updateReconciliation,
        deleteFn: deleteReconciliation,
        toForm: (row) => ({
          bank_account_id: row.bank_account_id != null ? String(row.bank_account_id) : "",
          statement_date: row.statement_date ?? "",
          statement_balance: row.statement_balance != null ? String(row.statement_balance) : "",
          book_balance: row.book_balance != null ? String(row.book_balance) : "",
        }),
        fromForm: (form) => ({
          bank_account_id: form.bank_account_id ? parseInt(form.bank_account_id) : undefined,
          statement_date: form.statement_date,
          statement_balance: form.statement_balance ? parseFloat(form.statement_balance) : 0,
          book_balance: form.book_balance ? parseFloat(form.book_balance) : undefined,
        }),
      }}
    />
  );
}
