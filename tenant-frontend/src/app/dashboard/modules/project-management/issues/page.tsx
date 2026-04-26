"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listPmIssues, createPmIssue, updatePmIssue, deletePmIssue } from "@/lib/pm-resources";

type Issue = { id: number; title: string; issue_type: string; severity: string; status: string; reported_by: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Issue>[] => [
  { accessorKey: "title", header: t("pm.title", "Title"), meta: { searchable: true } },
  { accessorKey: "issue_type", header: t("pm.type", "Type") },
  { accessorKey: "severity", header: t("pm.severity", "Severity") },
  { accessorKey: "status", header: t("pm.status", "Status") },
  { accessorKey: "reported_by", header: t("pm.reported_by", "Reported By") },
];

const fields: FieldDef[] = [
  { name: "title", label: "Title", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "issue_type", label: "Type", type: "select", required: true, options: [
    { value: "bug", label: "Bug" }, { value: "defect", label: "Defect" },
    { value: "enhancement", label: "Enhancement" }, { value: "question", label: "Question" },
  ]},
  { name: "severity", label: "Severity", type: "select", required: true, options: [
    { value: "trivial", label: "Trivial" }, { value: "minor", label: "Minor" },
    { value: "major", label: "Major" }, { value: "critical", label: "Critical" },
  ]},
  { name: "status", label: "Status", type: "select", options: [
    { value: "open", label: "Open" }, { value: "in_progress", label: "In Progress" },
    { value: "resolved", label: "Resolved" }, { value: "closed", label: "Closed" },
  ]},
  { name: "steps_to_reproduce", label: "Steps to Reproduce", type: "textarea" },
];

export default function IssuesPage() {
  return (
    <SimpleCRUDPage<Issue>
      config={{
        titleKey: "pm.issues",
        titleFallback: "Issues",
        subtitleKey: "pm.issues_subtitle",
        subtitleFallback: "Track and resolve project issues",
        createLabelKey: "pm.add_issue",
        createLabelFallback: "Add Issue",
        moduleKey: "project_management",
        dashboardHref: "/dashboard/modules/project-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listPmIssues<Issue>(params),
        createFn: createPmIssue,
        updateFn: (id: number, p: Record<string, unknown>) => updatePmIssue(id, p),
        deleteFn: deletePmIssue,
        toForm: (row) => ({
          title: row.title ?? "", issue_type: row.issue_type ?? "bug",
          severity: row.severity ?? "minor", status: row.status ?? "open",
        }),
        fromForm: (form) => ({
          title: form.title, issue_type: form.issue_type || "bug",
          severity: form.severity || "minor", status: form.status || "open",
        }),
      }}
    />
  );
}
