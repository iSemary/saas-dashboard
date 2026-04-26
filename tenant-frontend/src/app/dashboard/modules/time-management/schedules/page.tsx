"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listTmWorkSchedules, createTmWorkSchedule, updateTmWorkSchedule, deleteTmWorkSchedule } from "@/lib/tm-resources";

type Schedule = { id: number; name: string; effective_from: string; effective_to: string; schedule_type: string; is_active: boolean };

const columns = (t: (k: string, f: string) => string): ColumnDef<Schedule>[] => [
  { accessorKey: "name", header: t("tm.name", "Name"), meta: { searchable: true } },
  { accessorKey: "schedule_type", header: t("tm.type", "Type") },
  { accessorKey: "effective_from", header: t("tm.from", "From") },
  { accessorKey: "effective_to", header: t("tm.to", "To") },
  { accessorKey: "is_active", header: t("tm.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "schedule_type", label: "Type", type: "select", options: [
    { value: "fixed", label: "Fixed" }, { value: "flexible", label: "Flexible" },
    { value: "rotating", label: "Rotating" }, { value: "compressed", label: "Compressed" },
  ]},
  { name: "effective_from", label: "Effective From", required: true },
  { name: "effective_to", label: "Effective To" },
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
];

export default function SchedulesPage() {
  return (
    <SimpleCRUDPage<Schedule>
      config={{
        titleKey: "tm.schedules",
        titleFallback: "Schedules",
        subtitleKey: "tm.schedules_subtitle",
        subtitleFallback: "Manage work schedules",
        createLabelKey: "tm.add_schedule",
        createLabelFallback: "Add Schedule",
        moduleKey: "time_management",
        dashboardHref: "/dashboard/modules/time-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listTmWorkSchedules<Schedule>(params),
        createFn: createTmWorkSchedule,
        updateFn: (id: number, p: Record<string, unknown>) => updateTmWorkSchedule(id, p),
        deleteFn: deleteTmWorkSchedule,
        toForm: (row) => ({
          name: row.name ?? "", schedule_type: row.schedule_type ?? "fixed",
          effective_from: row.effective_from ?? "", effective_to: row.effective_to ?? "",
          is_active: row.is_active ? "1" : "0",
        }),
        fromForm: (form) => ({
          name: form.name, schedule_type: form.schedule_type || "fixed",
          effective_from: form.effective_from, effective_to: form.effective_to || undefined,
          is_active: form.is_active === "1",
        }),
      }}
    />
  );
}
