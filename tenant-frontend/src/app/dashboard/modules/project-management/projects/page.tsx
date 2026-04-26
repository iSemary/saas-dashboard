"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listPmProjects, createPmProject, updatePmProject, deletePmProject, archivePmProject, pausePmProject, completePmProject } from "@/lib/pm-resources";
import { toast } from "sonner";
import { Button } from "@/components/ui/button";

type Project = { id: number; name: string; key: string; status: string; priority: string; start_date: string; end_date: string; progress: number };

const columns = (t: (k: string, f: string) => string): ColumnDef<Project>[] => [
  { accessorKey: "name", header: t("pm.name", "Name"), meta: { searchable: true } },
  { accessorKey: "key", header: t("pm.key", "Key") },
  { accessorKey: "status", header: t("pm.status", "Status") },
  { accessorKey: "priority", header: t("pm.priority", "Priority") },
  { accessorKey: "start_date", header: t("pm.start_date", "Start Date") },
  { accessorKey: "progress", header: t("pm.progress", "Progress") },
  {
    id: "actions_extra",
    header: "",
    cell: ({ row }) => {
      const s = row.original.status;
      return (
        <div className="flex gap-1">
          {(s === "active" || s === "planning") && (
            <Button variant="outline" size="sm" className="h-7 text-xs" onClick={() => pausePmProject(row.original.id).then(() => toast.success("Project paused")).catch(() => toast.error("Failed to pause"))}>Pause</Button>
          )}
          {(s === "active" || s === "on_hold" || s === "planning") && (
            <Button variant="outline" size="sm" className="h-7 text-xs" onClick={() => completePmProject(row.original.id).then(() => toast.success("Project completed")).catch(() => toast.error("Failed to complete"))}>Complete</Button>
          )}
          {s !== "archived" && (
            <Button variant="outline" size="sm" className="h-7 text-xs" onClick={() => archivePmProject(row.original.id).then(() => toast.success("Project archived")).catch(() => toast.error("Failed to archive"))}>Archive</Button>
          )}
        </div>
      );
    },
  },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "key", label: "Key", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "planning", label: "Planning" }, { value: "active", label: "Active" },
    { value: "on_hold", label: "On Hold" }, { value: "completed", label: "Completed" },
    { value: "cancelled", label: "Cancelled" },
  ]},
  { name: "priority", label: "Priority", type: "select", options: [
    { value: "low", label: "Low" }, { value: "medium", label: "Medium" },
    { value: "high", label: "High" }, { value: "critical", label: "Critical" },
  ]},
  { name: "start_date", label: "Start Date" },
  { name: "end_date", label: "End Date" },
];

export default function ProjectsPage() {
  return (
    <SimpleCRUDPage<Project>
      config={{
        titleKey: "pm.projects",
        titleFallback: "Projects",
        subtitleKey: "pm.projects_subtitle",
        subtitleFallback: "Manage your projects",
        createLabelKey: "pm.add_project",
        createLabelFallback: "Add Project",
        moduleKey: "project_management",
        dashboardHref: "/dashboard/modules/project-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listPmProjects<Project>(params),
        createFn: createPmProject,
        updateFn: (id: number, p: Record<string, unknown>) => updatePmProject(id, p),
        deleteFn: deletePmProject,
        toForm: (row) => ({
          name: row.name ?? "", key: row.key ?? "", status: row.status ?? "planning",
          priority: row.priority ?? "medium", start_date: row.start_date ?? "", end_date: row.end_date ?? "",
        }),
        fromForm: (form) => ({
          name: form.name, key: form.key, status: form.status || "planning",
          priority: form.priority || "medium",
          start_date: form.start_date || undefined, end_date: form.end_date || undefined,
        }),
      }}
    />
  );
}
