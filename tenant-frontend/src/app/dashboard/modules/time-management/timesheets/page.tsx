"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listTmTimesheets, createTmTimesheet, updateTmTimesheet, deleteTmTimesheet, submitTmTimesheet, approveTmTimesheet, rejectTmTimesheet } from "@/lib/tm-resources";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";

type Timesheet = { id: number; period_start: string; period_end: string; status: string; total_hours: number; employee_name: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Timesheet>[] => [
  { accessorKey: "period_start", header: t("tm.period_start", "Period Start") },
  { accessorKey: "period_end", header: t("tm.period_end", "Period End") },
  { accessorKey: "status", header: t("tm.status", "Status") },
  { accessorKey: "total_hours", header: t("tm.total_hours", "Total Hours") },
  { accessorKey: "employee_name", header: t("tm.employee", "Employee") },
  {
    id: "workflow_actions",
    header: "",
    cell: ({ row }) => {
      const s = row.original.status;
      return (
        <div className="flex gap-1">
          {s === "Draft" && (
            <Button variant="outline" size="sm" className="h-7 text-xs" onClick={() => submitTmTimesheet(row.original.id).then(() => toast.success("Timesheet submitted")).catch(() => toast.error("Failed to submit"))}>Submit</Button>
          )}
          {s === "Submitted" && (
            <>
              <Button variant="outline" size="sm" className="h-7 text-xs" onClick={() => approveTmTimesheet(row.original.id).then(() => toast.success("Timesheet approved")).catch(() => toast.error("Failed to approve"))}>Approve</Button>
              <Button variant="outline" size="sm" className="h-7 text-xs text-destructive" onClick={() => rejectTmTimesheet(row.original.id, { reason: "Rejected" }).then(() => toast.success("Timesheet rejected")).catch(() => toast.error("Failed to reject"))}>Reject</Button>
            </>
          )}
        </div>
      );
    },
  },
];

const fields: FieldDef[] = [
  { name: "period_start", label: "Period Start", required: true },
  { name: "period_end", label: "Period End", required: true },
  { name: "status", label: "Status", type: "select", options: [
    { value: "Draft", label: "Draft" }, { value: "Submitted", label: "Submitted" },
    { value: "Approved", label: "Approved" }, { value: "Rejected", label: "Rejected" },
  ]},
  { name: "notes", label: "Notes", type: "textarea" },
];

export default function TimesheetsPage() {
  return (
    <SimpleCRUDPage<Timesheet>
      config={{
        titleKey: "tm.timesheets",
        titleFallback: "Timesheets",
        subtitleKey: "tm.timesheets_subtitle",
        subtitleFallback: "Review and manage timesheets",
        createLabelKey: "tm.add_timesheet",
        createLabelFallback: "Add Timesheet",
        moduleKey: "time_management",
        dashboardHref: "/dashboard/modules/time-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listTmTimesheets<Timesheet>(params),
        createFn: createTmTimesheet,
        updateFn: (id: number, p: Record<string, unknown>) => updateTmTimesheet(id, p),
        deleteFn: deleteTmTimesheet,
        toForm: (row) => ({
          period_start: row.period_start ?? "", period_end: row.period_end ?? "",
          status: row.status ?? "Draft",
        }),
        fromForm: (form) => ({
          period_start: form.period_start, period_end: form.period_end,
          status: form.status || "Draft", notes: form.notes || undefined,
        }),
      }}
    />
  );
}
