"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmTemplates, createSmTemplate, updateSmTemplate, deleteSmTemplate, type SmTemplate } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmTemplate>[] => [
  { accessorKey: "name", header: t("sms_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "body", header: t("sms_marketing.body", "Body") },
  { accessorKey: "status", header: t("sms_marketing.status", "Status") },
  { accessorKey: "created_at", header: t("sms_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "body", label: "Body", type: "textarea", required: true },
  { name: "status", label: "Status", type: "select", options: [
    { value: "draft", label: "Draft" }, { value: "active", label: "Active" }, { value: "archived", label: "Archived" },
  ]},
];

export default function SmTemplatesPage() {
  return (
    <SimpleCRUDPage<SmTemplate>
      config={{
        titleKey: "sms_marketing.templates",
        titleFallback: "Templates",
        subtitleKey: "sms_marketing.templates_subtitle",
        subtitleFallback: "Manage SMS templates",
        createLabelKey: "sms_marketing.add_template",
        createLabelFallback: "Add Template",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmTemplates(params),
        createFn: createSmTemplate,
        updateFn: updateSmTemplate,
        deleteFn: deleteSmTemplate,
        toForm: (row) => ({
          name: row.name ?? "", body: row.body ?? "", status: row.status ?? "draft",
        }),
        fromForm: (form) => ({
          name: form.name, body: form.body, status: form.status || "draft",
        }),
      }}
    />
  );
}
