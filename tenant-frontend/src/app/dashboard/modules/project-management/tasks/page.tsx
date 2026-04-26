"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listPmTasks, createPmTask, updatePmTask, deletePmTask } from "@/lib/pm-resources";

type Task = { id: number; title: string; status: string; priority: string; task_type: string; due_date: string; progress: number };

const columns = (t: (k: string, f: string) => string): ColumnDef<Task>[] => [
  { accessorKey: "title", header: t("pm.title", "Title"), meta: { searchable: true } },
  { accessorKey: "status", header: t("pm.status", "Status") },
  { accessorKey: "priority", header: t("pm.priority", "Priority") },
  { accessorKey: "task_type", header: t("pm.type", "Type") },
  { accessorKey: "due_date", header: t("pm.due_date", "Due Date") },
  { accessorKey: "progress", header: t("pm.progress", "Progress") },
];

const fields: FieldDef[] = [
  { name: "title", label: "Title", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "todo", label: "To Do" }, { value: "in_progress", label: "In Progress" },
    { value: "in_review", label: "In Review" }, { value: "done", label: "Done" },
  ]},
  { name: "priority", label: "Priority", type: "select", options: [
    { value: "low", label: "Low" }, { value: "medium", label: "Medium" },
    { value: "high", label: "High" }, { value: "critical", label: "Critical" },
  ]},
  { name: "task_type", label: "Type", type: "select", options: [
    { value: "task", label: "Task" }, { value: "bug", label: "Bug" },
    { value: "feature", label: "Feature" }, { value: "improvement", label: "Improvement" },
  ]},
  { name: "due_date", label: "Due Date" },
  { name: "estimated_hours", label: "Estimated Hours", type: "number" },
];

export default function TasksPage() {
  return (
    <SimpleCRUDPage<Task>
      config={{
        titleKey: "pm.tasks",
        titleFallback: "Tasks",
        subtitleKey: "pm.tasks_subtitle",
        subtitleFallback: "Manage project tasks",
        createLabelKey: "pm.add_task",
        createLabelFallback: "Add Task",
        moduleKey: "project_management",
        dashboardHref: "/dashboard/modules/project-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listPmTasks<Task>(params),
        createFn: createPmTask,
        updateFn: (id: number, p: Record<string, unknown>) => updatePmTask(id, p),
        deleteFn: deletePmTask,
        toForm: (row) => ({
          title: row.title ?? "", status: row.status ?? "todo",
          priority: row.priority ?? "medium", task_type: row.task_type ?? "task",
          due_date: row.due_date ?? "",
          estimated_hours: row.progress != null ? String(row.progress) : "",
        }),
        fromForm: (form) => ({
          title: form.title, status: form.status || "todo",
          priority: form.priority || "medium", task_type: form.task_type || "task",
          due_date: form.due_date || undefined,
          estimated_hours: form.estimated_hours ? parseFloat(form.estimated_hours) : undefined,
        }),
      }}
    />
  );
}
