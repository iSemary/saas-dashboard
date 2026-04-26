"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams, PaginatedResponse } from "@/lib/tenant-resources";
import { listJournalEntries, createJournalEntry, updateJournalEntry, deleteJournalEntry } from "@/lib/accounting-resources";

type JournalEntry = { id: number; entry_number: string; entry_date: string; state: string; reference: string; total_debit: number; total_credit: number };

const columns = (t: (k: string, f: string) => string): ColumnDef<JournalEntry>[] => [
  { accessorKey: "entry_number", header: t("accounting.entry_number", "Entry #"), meta: { searchable: true } },
  { accessorKey: "entry_date", header: t("accounting.date", "Date") },
  { accessorKey: "state", header: t("accounting.state", "State") },
  { accessorKey: "reference", header: t("accounting.reference", "Reference") },
  { accessorKey: "total_debit", header: t("accounting.debit", "Debit") },
  { accessorKey: "total_credit", header: t("accounting.credit", "Credit") },
];

const fields: FieldDef[] = [
  { name: "entry_date", label: "Date", type: "text", required: true },
  { name: "reference", label: "Reference" },
  { name: "description", label: "Description", type: "textarea" },
  { name: "fiscal_year_id", label: "Fiscal Year", type: "number" },
];

export default function JournalEntriesPage() {
  return (
    <SimpleCRUDPage<JournalEntry>
      config={{
        titleKey: "accounting.journal_entries",
        titleFallback: "Journal Entries",
        subtitleKey: "accounting.journal_entries_subtitle",
        subtitleFallback: "Manage your journal entries",
        createLabelKey: "accounting.add_entry",
        createLabelFallback: "Add Entry",
        moduleKey: "accounting",
        dashboardHref: "/dashboard/modules/accounting",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listJournalEntries<JournalEntry>(params),
        createFn: createJournalEntry,
        updateFn: updateJournalEntry,
        deleteFn: deleteJournalEntry,
        toForm: (row) => ({
          entry_date: row.entry_date ?? "", reference: row.reference ?? "",
        }),
        fromForm: (form) => ({
          entry_date: form.entry_date, reference: form.reference || undefined,
        }),
      }}
    />
  );
}
