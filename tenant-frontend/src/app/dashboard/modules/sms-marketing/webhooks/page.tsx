"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmWebhooks, createSmWebhook, updateSmWebhook, deleteSmWebhook, type SmWebhook } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmWebhook>[] => [
  { accessorKey: "name", header: t("sms_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "url", header: t("sms_marketing.url", "URL") },
  { accessorKey: "is_active", header: t("sms_marketing.is_active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
  { accessorKey: "created_at", header: t("sms_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "url", label: "URL", required: true },
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
];

export default function SmWebhooksPage() {
  return (
    <SimpleCRUDPage<SmWebhook>
      config={{
        titleKey: "sms_marketing.webhooks",
        titleFallback: "Webhooks",
        subtitleKey: "sms_marketing.webhooks_subtitle",
        subtitleFallback: "Manage webhooks",
        createLabelKey: "sms_marketing.add_webhook",
        createLabelFallback: "Add Webhook",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmWebhooks(params),
        createFn: createSmWebhook,
        updateFn: updateSmWebhook,
        deleteFn: deleteSmWebhook,
        toForm: (row) => ({
          name: row.name ?? "", url: row.url ?? "", is_active: row.is_active ? "1" : "0",
        }),
        fromForm: (form) => ({
          name: form.name, url: form.url, is_active: form.is_active === "1",
        }),
      }}
    />
  );
}
