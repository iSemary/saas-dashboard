"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmWebhooks, createEmWebhook, updateEmWebhook, deleteEmWebhook, type EmWebhook } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmWebhook>[] => [
  { accessorKey: "name", header: t("email_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "url", header: t("email_marketing.url", "URL") },
  { accessorKey: "is_active", header: t("email_marketing.is_active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
  { accessorKey: "created_at", header: t("email_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "url", label: "URL", required: true },
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
];

export default function EmWebhooksPage() {
  return (
    <SimpleCRUDPage<EmWebhook>
      config={{
        titleKey: "email_marketing.webhooks",
        titleFallback: "Webhooks",
        subtitleKey: "email_marketing.webhooks_subtitle",
        subtitleFallback: "Manage webhooks",
        createLabelKey: "email_marketing.add_webhook",
        createLabelFallback: "Add Webhook",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmWebhooks(params),
        createFn: createEmWebhook,
        updateFn: updateEmWebhook,
        deleteFn: deleteEmWebhook,
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
