"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listTmTimeEntries, createTmTimeEntry, updateTmTimeEntry, deleteTmTimeEntry } from "@/lib/tm-resources";

type TimeEntry = { id: number; description: string; start_time: string; end_time: string; duration: number; is_billable: boolean; project_name: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<TimeEntry>[] => [
  { accessorKey: "description", header: t("tm.description", "Description"), meta: { searchable: true } },
  { accessorKey: "start_time", header: t("tm.start", "Start") },
  { accessorKey: "end_time", header: t("tm.end", "End") },
  { accessorKey: "duration", header: t("tm.duration", "Duration (min)") },
  { accessorKey: "is_billable", header: t("tm.billable", "Billable"), cell: ({ row }) => row.original.is_billable ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "description", label: "Description", required: true },
  { name: "start_time", label: "Start Time", required: true },
  { name: "end_time", label: "End Time" },
  { name: "duration", label: "Duration (minutes)", type: "number" },
  { name: "is_billable", label: "Billable", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "project_id", label: "Project ID", type: "number" },
  { name: "task_id", label: "Task ID", type: "number" },
];

export default function TimeEntriesPage() {
  return (
    <SimpleCRUDPage<TimeEntry>
      config={{
        titleKey: "tm.time_entries",
        titleFallback: "Time Entries",
        subtitleKey: "tm.time_entries_subtitle",
        subtitleFallback: "Track your time entries",
        createLabelKey: "tm.add_time_entry",
        createLabelFallback: "Add Time Entry",
        moduleKey: "time_management",
        dashboardHref: "/dashboard/modules/time-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listTmTimeEntries<TimeEntry>(params),
        createFn: createTmTimeEntry,
        updateFn: (id: number, p: Record<string, unknown>) => updateTmTimeEntry(id, p),
        deleteFn: deleteTmTimeEntry,
        toForm: (row) => ({
          description: row.description ?? "", start_time: row.start_time ?? "",
          end_time: row.end_time ?? "", duration: row.duration != null ? String(row.duration) : "",
          is_billable: row.is_billable ? "1" : "0",
        }),
        fromForm: (form) => ({
          description: form.description, start_time: form.start_time,
          end_time: form.end_time || undefined,
          duration: form.duration ? parseFloat(form.duration) : undefined,
          is_billable: form.is_billable === "1",
        }),
      }}
    />
  );
}
