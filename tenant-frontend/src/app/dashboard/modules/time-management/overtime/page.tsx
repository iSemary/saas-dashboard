"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listTmOvertimeRequests, createTmOvertimeRequest, approveTmOvertimeRequest, rejectTmOvertimeRequest } from "@/lib/tm-resources";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";

type Overtime = { id: number; date: string; start_time: string; end_time: string; hours: number; reason: string; status: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Overtime>[] => [
  { accessorKey: "date", header: t("tm.date", "Date") },
  { accessorKey: "hours", header: t("tm.hours", "Hours") },
  { accessorKey: "reason", header: t("tm.reason", "Reason") },
  { accessorKey: "status", header: t("tm.status", "Status") },
  {
    id: "workflow_actions",
    header: "",
    cell: ({ row }) => {
      const s = row.original.status;
      if (s !== "pending") return null;
      return (
        <div className="flex gap-1">
          <Button variant="outline" size="sm" className="h-7 text-xs" onClick={() => approveTmOvertimeRequest(row.original.id).then(() => toast.success("Overtime approved")).catch(() => toast.error("Failed to approve"))}>Approve</Button>
          <Button variant="outline" size="sm" className="h-7 text-xs text-destructive" onClick={() => rejectTmOvertimeRequest(row.original.id, { reason: "Rejected" }).then(() => toast.success("Overtime rejected")).catch(() => toast.error("Failed to reject"))}>Reject</Button>
        </div>
      );
    },
  },
];

const fields: FieldDef[] = [
  { name: "date", label: "Date", required: true },
  { name: "start_time", label: "Start Time", required: true },
  { name: "end_time", label: "End Time", required: true },
  { name: "hours", label: "Hours", type: "number", required: true },
  { name: "reason", label: "Reason", type: "textarea", required: true },
  { name: "status", label: "Status", type: "select", options: [
    { value: "pending", label: "Pending" }, { value: "approved", label: "Approved" },
    { value: "rejected", label: "Rejected" },
  ]},
];

export default function OvertimePage() {
  return (
    <SimpleCRUDPage<Overtime>
      config={{
        titleKey: "tm.overtime",
        titleFallback: "Overtime",
        subtitleKey: "tm.overtime_subtitle",
        subtitleFallback: "Manage overtime requests",
        createLabelKey: "tm.add_overtime",
        createLabelFallback: "Request Overtime",
        moduleKey: "time_management",
        dashboardHref: "/dashboard/modules/time-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listTmOvertimeRequests<Overtime>(params),
        createFn: createTmOvertimeRequest,
        updateFn: undefined,
        deleteFn: undefined,
        toForm: (row) => ({
          date: row.date ?? "", start_time: row.start_time ?? "", end_time: row.end_time ?? "",
          hours: row.hours != null ? String(row.hours) : "", reason: row.reason ?? "",
          status: row.status ?? "pending",
        }),
        fromForm: (form) => ({
          date: form.date, start_time: form.start_time, end_time: form.end_time,
          hours: form.hours ? parseFloat(form.hours) : 0, reason: form.reason,
          status: form.status || "pending",
        }),
      }}
    />
  );
}
