"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listPmRisks, createPmRisk, updatePmRisk, deletePmRisk } from "@/lib/pm-resources";

type Risk = { id: number; title: string; severity: string; probability: string; status: string; mitigation: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Risk>[] => [
  { accessorKey: "title", header: t("pm.title", "Title"), meta: { searchable: true } },
  { accessorKey: "severity", header: t("pm.severity", "Severity") },
  { accessorKey: "probability", header: t("pm.probability", "Probability") },
  { accessorKey: "status", header: t("pm.status", "Status") },
];

const fields: FieldDef[] = [
  { name: "title", label: "Title", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "severity", label: "Severity", type: "select", required: true, options: [
    { value: "low", label: "Low" }, { value: "medium", label: "Medium" },
    { value: "high", label: "High" }, { value: "critical", label: "Critical" },
  ]},
  { name: "probability", label: "Probability", type: "select", required: true, options: [
    { value: "unlikely", label: "Unlikely" }, { value: "possible", label: "Possible" },
    { value: "likely", label: "Likely" }, { value: "certain", label: "Certain" },
  ]},
  { name: "status", label: "Status", type: "select", options: [
    { value: "identified", label: "Identified" }, { value: "analyzing", label: "Analyzing" },
    { value: "mitigating", label: "Mitigating" }, { value: "resolved", label: "Resolved" },
    { value: "accepted", label: "Accepted" },
  ]},
  { name: "mitigation", label: "Mitigation Plan", type: "textarea" },
  { name: "impact", label: "Impact", type: "textarea" },
];

export default function RisksPage() {
  return (
    <SimpleCRUDPage<Risk>
      config={{
        titleKey: "pm.risks",
        titleFallback: "Risks",
        subtitleKey: "pm.risks_subtitle",
        subtitleFallback: "Identify and manage project risks",
        createLabelKey: "pm.add_risk",
        createLabelFallback: "Add Risk",
        moduleKey: "project_management",
        dashboardHref: "/dashboard/modules/project-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listPmRisks<Risk>(params),
        createFn: createPmRisk,
        updateFn: (id: number, p: Record<string, unknown>) => updatePmRisk(id, p),
        deleteFn: deletePmRisk,
        toForm: (row) => ({
          title: row.title ?? "", severity: row.severity ?? "medium",
          probability: row.probability ?? "possible", status: row.status ?? "identified",
          mitigation: row.mitigation ?? "",
        }),
        fromForm: (form) => ({
          title: form.title, severity: form.severity || "medium",
          probability: form.probability || "possible", status: form.status || "identified",
          mitigation: form.mitigation || undefined,
        }),
      }}
    />
  );
}
