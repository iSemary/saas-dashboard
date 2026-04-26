"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listReimbursements, createReimbursement, updateReimbursement, deleteReimbursement } from "@/lib/expenses-resources";

type Reimbursement = { id: number; reference: string; amount: number; currency: string; status: string; payment_method: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Reimbursement>[] => [
  { accessorKey: "reference", header: t("expenses.reference", "Reference"), meta: { searchable: true } },
  { accessorKey: "amount", header: t("expenses.amount", "Amount") },
  { accessorKey: "currency", header: t("expenses.currency", "Currency") },
  { accessorKey: "status", header: t("expenses.status", "Status") },
  { accessorKey: "payment_method", header: t("expenses.payment_method", "Payment Method") },
];

const fields: FieldDef[] = [
  { name: "reference", label: "Reference", required: true },
  { name: "amount", label: "Amount", type: "number", required: true },
  { name: "currency", label: "Currency" },
  { name: "payment_method", label: "Payment Method" },
  { name: "notes", label: "Notes", type: "textarea" },
];

export default function ReimbursementsPage() {
  return (
    <SimpleCRUDPage<Reimbursement>
      config={{
        titleKey: "expenses.reimbursements",
        titleFallback: "Reimbursements",
        subtitleKey: "expenses.reimbursements_subtitle",
        subtitleFallback: "Manage reimbursements",
        createLabelKey: "expenses.add_reimbursement",
        createLabelFallback: "Add Reimbursement",
        moduleKey: "expenses",
        dashboardHref: "/dashboard/modules/expenses",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listReimbursements<Reimbursement>(params),
        createFn: createReimbursement,
        updateFn: updateReimbursement,
        deleteFn: deleteReimbursement,
        toForm: (row) => ({
          reference: row.reference ?? "", amount: row.amount != null ? String(row.amount) : "",
          currency: row.currency ?? "USD", payment_method: row.payment_method ?? "",
        }),
        fromForm: (form) => ({
          reference: form.reference, amount: form.amount ? parseFloat(form.amount) : 0,
          currency: form.currency || "USD", payment_method: form.payment_method || undefined,
        }),
      }}
    />
  );
}
