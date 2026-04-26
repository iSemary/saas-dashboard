"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listPmTemplates, createPmTemplate, updatePmTemplate, deletePmTemplate } from "@/lib/pm-resources";

type Template = { id: number; name: string; description: string; category: string; is_public: boolean };

const columns = (t: (k: string, f: string) => string): ColumnDef<Template>[] => [
  { accessorKey: "name", header: t("pm.name", "Name"), meta: { searchable: true } },
  { accessorKey: "category", header: t("pm.category", "Category") },
  { accessorKey: "description", header: t("pm.description", "Description") },
  { accessorKey: "is_public", header: t("pm.public", "Public"), cell: ({ row }) => row.original.is_public ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "category", label: "Category", type: "select", options: [
    { value: "software", label: "Software" }, { value: "marketing", label: "Marketing" },
    { value: "operations", label: "Operations" }, { value: "general", label: "General" },
  ]},
  { name: "is_public", label: "Public", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
];

export default function TemplatesPage() {
  return (
    <SimpleCRUDPage<Template>
      config={{
        titleKey: "pm.templates",
        titleFallback: "Templates",
        subtitleKey: "pm.templates_subtitle",
        subtitleFallback: "Reusable project templates",
        createLabelKey: "pm.add_template",
        createLabelFallback: "Add Template",
        moduleKey: "project_management",
        dashboardHref: "/dashboard/modules/project-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listPmTemplates<Template>(params),
        createFn: createPmTemplate,
        updateFn: (id: number, p: Record<string, unknown>) => updatePmTemplate(id, p),
        deleteFn: deletePmTemplate,
        toForm: (row) => ({
          name: row.name ?? "", category: row.category ?? "general",
          description: row.description ?? "", is_public: row.is_public ? "1" : "0",
        }),
        fromForm: (form) => ({
          name: form.name, category: form.category || "general",
          description: form.description || undefined, is_public: form.is_public === "1",
        }),
      }}
    />
  );
}
