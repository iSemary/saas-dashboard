"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listPmMilestones, createPmMilestone, updatePmMilestone, deletePmMilestone } from "@/lib/pm-resources";

type Milestone = { id: number; name: string; status: string; due_date: string; progress: number };

const columns = (t: (k: string, f: string) => string): ColumnDef<Milestone>[] => [
  { accessorKey: "name", header: t("pm.name", "Name"), meta: { searchable: true } },
  { accessorKey: "status", header: t("pm.status", "Status") },
  { accessorKey: "due_date", header: t("pm.due_date", "Due Date") },
  { accessorKey: "progress", header: t("pm.progress", "Progress") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "planned", label: "Planned" }, { value: "in_progress", label: "In Progress" },
    { value: "completed", label: "Completed" }, { value: "overdue", label: "Overdue" },
  ]},
  { name: "due_date", label: "Due Date" },
];

export default function MilestonesPage() {
  return (
    <SimpleCRUDPage<Milestone>
      config={{
        titleKey: "pm.milestones",
        titleFallback: "Milestones",
        subtitleKey: "pm.milestones_subtitle",
        subtitleFallback: "Track project milestones",
        createLabelKey: "pm.add_milestone",
        createLabelFallback: "Add Milestone",
        moduleKey: "project_management",
        dashboardHref: "/dashboard/modules/project-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listPmMilestones<Milestone>(params),
        createFn: createPmMilestone,
        updateFn: (id: number, p: Record<string, unknown>) => updatePmMilestone(id, p),
        deleteFn: deletePmMilestone,
        toForm: (row) => ({
          name: row.name ?? "", status: row.status ?? "planned", due_date: row.due_date ?? "",
        }),
        fromForm: (form) => ({
          name: form.name, status: form.status || "planned", due_date: form.due_date || undefined,
        }),
      }}
    />
  );
}
