"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmCampaigns, createSmCampaign, updateSmCampaign, deleteSmCampaign, type SmCampaign } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmCampaign>[] => [
  { accessorKey: "name", header: t("sms_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "body", header: t("sms_marketing.body", "Body") },
  { accessorKey: "status", header: t("sms_marketing.status", "Status") },
  { accessorKey: "scheduled_at", header: t("sms_marketing.scheduled_at", "Scheduled At") },
  { accessorKey: "created_at", header: t("sms_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "body", label: "Body", type: "textarea", required: true },
  { name: "template_id", label: "Template ID", type: "number" },
  { name: "credential_id", label: "Credential ID", type: "number" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "draft", label: "Draft" }, { value: "scheduled", label: "Scheduled" },
    { value: "sending", label: "Sending" }, { value: "sent", label: "Sent" },
    { value: "paused", label: "Paused" }, { value: "cancelled", label: "Cancelled" },
  ]},
  { name: "scheduled_at", label: "Scheduled At", type: "text" },
];

export default function SmCampaignsPage() {
  return (
    <SimpleCRUDPage<SmCampaign>
      config={{
        titleKey: "sms_marketing.campaigns",
        titleFallback: "Campaigns",
        subtitleKey: "sms_marketing.campaigns_subtitle",
        subtitleFallback: "Manage SMS campaigns",
        createLabelKey: "sms_marketing.add_campaign",
        createLabelFallback: "Add Campaign",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmCampaigns(params),
        createFn: createSmCampaign,
        updateFn: updateSmCampaign,
        deleteFn: deleteSmCampaign,
        toForm: (row) => ({
          name: row.name ?? "", body: row.body ?? "", template_id: row.template_id?.toString() ?? "",
          credential_id: row.credential_id?.toString() ?? "", status: row.status ?? "draft",
          scheduled_at: row.scheduled_at ?? "",
        }),
        fromForm: (form) => ({
          name: form.name, body: form.body,
          template_id: form.template_id ? Number(form.template_id) : undefined,
          credential_id: form.credential_id ? Number(form.credential_id) : undefined,
          status: form.status || "draft", scheduled_at: form.scheduled_at || undefined,
        }),
      }}
    />
  );
}
