"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmTemplates, createEmTemplate, updateEmTemplate, deleteEmTemplate, type EmTemplate } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmTemplate>[] => [
  { accessorKey: "name", header: t("email_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "subject", header: t("email_marketing.subject", "Subject") },
  { accessorKey: "category", header: t("email_marketing.category", "Category") },
  { accessorKey: "status", header: t("email_marketing.status", "Status") },
  { accessorKey: "created_at", header: t("email_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "subject", label: "Subject" },
  { name: "body_html", label: "Body HTML", type: "textarea" },
  { name: "body_text", label: "Body Text", type: "textarea" },
  { name: "category", label: "Category" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "draft", label: "Draft" }, { value: "active", label: "Active" }, { value: "archived", label: "Archived" },
  ]},
];

export default function EmTemplatesPage() {
  return (
    <SimpleCRUDPage<EmTemplate>
      config={{
        titleKey: "email_marketing.templates",
        titleFallback: "Templates",
        subtitleKey: "email_marketing.templates_subtitle",
        subtitleFallback: "Manage email templates",
        createLabelKey: "email_marketing.add_template",
        createLabelFallback: "Add Template",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmTemplates(params),
        createFn: createEmTemplate,
        updateFn: updateEmTemplate,
        deleteFn: deleteEmTemplate,
        toForm: (row) => ({
          name: row.name ?? "", subject: row.subject ?? "", body_html: row.body_html ?? "",
          body_text: row.body_text ?? "", category: row.category ?? "", status: row.status ?? "draft",
        }),
        fromForm: (form) => ({
          name: form.name, subject: form.subject || undefined,
          body_html: form.body_html || undefined, body_text: form.body_text || undefined,
          category: form.category || undefined, status: form.status || "draft",
        }),
      }}
    />
  );
}
