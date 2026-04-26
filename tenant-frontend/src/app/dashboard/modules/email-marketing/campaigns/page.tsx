"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmCampaigns, createEmCampaign, updateEmCampaign, deleteEmCampaign, type EmCampaign } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmCampaign>[] => [
  { accessorKey: "name", header: t("email_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "subject", header: t("email_marketing.subject", "Subject") },
  { accessorKey: "status", header: t("email_marketing.status", "Status") },
  { accessorKey: "scheduled_at", header: t("email_marketing.scheduled_at", "Scheduled At") },
  { accessorKey: "created_at", header: t("email_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "subject", label: "Subject", required: true },
  { name: "template_id", label: "Template ID", type: "number" },
  { name: "credential_id", label: "Credential ID", type: "number" },
  { name: "from_name", label: "From Name" },
  { name: "from_email", label: "From Email" },
  { name: "body_html", label: "Body HTML", type: "textarea" },
  { name: "body_text", label: "Body Text", type: "textarea" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "draft", label: "Draft" }, { value: "scheduled", label: "Scheduled" },
    { value: "sending", label: "Sending" }, { value: "sent", label: "Sent" },
    { value: "paused", label: "Paused" }, { value: "cancelled", label: "Cancelled" },
  ]},
  { name: "scheduled_at", label: "Scheduled At", type: "text" },
];

export default function EmCampaignsPage() {
  return (
    <SimpleCRUDPage<EmCampaign>
      config={{
        titleKey: "email_marketing.campaigns",
        titleFallback: "Campaigns",
        subtitleKey: "email_marketing.campaigns_subtitle",
        subtitleFallback: "Manage email campaigns",
        createLabelKey: "email_marketing.add_campaign",
        createLabelFallback: "Add Campaign",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmCampaigns(params),
        createFn: createEmCampaign,
        updateFn: updateEmCampaign,
        deleteFn: deleteEmCampaign,
        toForm: (row) => ({
          name: row.name ?? "", subject: row.subject ?? "", template_id: row.template_id?.toString() ?? "",
          credential_id: row.credential_id?.toString() ?? "", from_name: row.from_name ?? "",
          from_email: row.from_email ?? "", body_html: row.body_html ?? "", body_text: row.body_text ?? "",
          status: row.status ?? "draft", scheduled_at: row.scheduled_at ?? "",
        }),
        fromForm: (form) => ({
          name: form.name, subject: form.subject, template_id: form.template_id ? Number(form.template_id) : undefined,
          credential_id: form.credential_id ? Number(form.credential_id) : undefined,
          from_name: form.from_name || undefined, from_email: form.from_email || undefined,
          body_html: form.body_html || undefined, body_text: form.body_text || undefined,
          status: form.status || "draft", scheduled_at: form.scheduled_at || undefined,
        }),
      }}
    />
  );
}
