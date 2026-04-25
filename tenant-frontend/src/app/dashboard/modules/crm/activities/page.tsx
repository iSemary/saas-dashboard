"use client";

import { ColumnDef } from "@tanstack/react-table";
import { Badge } from "@/components/ui/badge";
import { SimpleCRUDPage } from "@/components/simple-crud-page";
import {
  listCrmActivities,
  createCrmActivity,
  updateCrmActivity,
  deleteCrmActivity,
} from "@/lib/tenant-resources";

interface Activity {
  id: number;
  subject: string;
  type: string;
  status: string;
  due_date: string | null;
  description: string | null;
  related_type: string | null;
  related_id: number | null;
  assigned_to: number | null;
}

const TYPE_ICONS: Record<string, string> = {
  task: "📋", call: "📞", email: "📧", meeting: "🤝", note: "📝",
};

const STATUS_COLORS: Record<string, string> = {
  planned: "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300",
  in_progress: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300",
  completed: "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300",
  cancelled: "bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400",
};

const columns = (): Array<ColumnDef<Activity>> => [
  { accessorKey: "subject", header: "Subject" },
  {
    accessorKey: "type",
    header: "Type",
    cell: ({ row }) => <span>{TYPE_ICONS[row.original.type] ?? "🔹"} {row.original.type}</span>,
  },
  {
    accessorKey: "status",
    header: "Status",
    cell: ({ row }) => (
      <Badge className={STATUS_COLORS[row.original.status] ?? ""}>{row.original.status.replace("_", " ")}</Badge>
    ),
  },
  {
    accessorKey: "due_date",
    header: "Due Date",
    cell: ({ row }) => row.original.due_date ?? "—",
  },
];

export default function ActivitiesPage() {
  return (
    <SimpleCRUDPage<Activity>
      config={{
        titleKey: "dashboard.crm.activities",
        titleFallback: "Activities",
        subtitleKey: "dashboard.crm.activities_subtitle",
        subtitleFallback: "Manage your tasks, calls, meetings and more",
        createLabelKey: "dashboard.crm.add_activity",
        createLabelFallback: "Add Activity",
        moduleKey: "crm",
        dashboardHref: "/dashboard/modules/crm",
        serverSide: true,
        fields: [
          { name: "subject", label: "Subject", required: true },
          {
            name: "type",
            label: "Type",
            type: "select",
            options: [
              { value: "task", label: "Task" },
              { value: "call", label: "Call" },
              { value: "email", label: "Email" },
              { value: "meeting", label: "Meeting" },
              { value: "note", label: "Note" },
            ],
          },
          {
            name: "status",
            label: "Status",
            type: "select",
            options: [
              { value: "planned", label: "Planned" },
              { value: "in_progress", label: "In Progress" },
              { value: "completed", label: "Completed" },
              { value: "cancelled", label: "Cancelled" },
            ],
          },
          { name: "due_date", label: "Due Date", type: "text", placeholder: "YYYY-MM-DD HH:mm" },
          { name: "description", label: "Description", type: "textarea" },
        ],
        columns,
        listFn: listCrmActivities,
        createFn: createCrmActivity,
        updateFn: updateCrmActivity,
        deleteFn: deleteCrmActivity,
        toForm: (row) => ({
          subject: row.subject ?? "",
          type: row.type ?? "task",
          status: row.status ?? "planned",
          due_date: row.due_date ?? "",
          description: row.description ?? "",
        }),
        fromForm: (form) => ({
          subject: form.subject,
          type: form.type || "task",
          status: form.status || "planned",
          due_date: form.due_date || undefined,
          description: form.description || undefined,
        }),
      }}
    />
  );
}
