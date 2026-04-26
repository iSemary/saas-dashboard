"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listPmWebhooks, createPmWebhook, updatePmWebhook, deletePmWebhook } from "@/lib/pm-resources";

type Webhook = { id: number; url: string; events: string; is_active: boolean; last_triggered_at: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Webhook>[] => [
  { accessorKey: "url", header: t("pm.url", "URL"), meta: { searchable: true } },
  { accessorKey: "events", header: t("pm.events", "Events") },
  { accessorKey: "is_active", header: t("pm.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
  { accessorKey: "last_triggered_at", header: t("pm.last_triggered", "Last Triggered") },
];

const fields: FieldDef[] = [
  { name: "url", label: "URL", type: "url", required: true },
  { name: "events", label: "Events (comma-separated)", required: true },
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "secret", label: "Secret" },
];

export default function WebhooksPage() {
  return (
    <SimpleCRUDPage<Webhook>
      config={{
        titleKey: "pm.webhooks",
        titleFallback: "Webhooks",
        subtitleKey: "pm.webhooks_subtitle",
        subtitleFallback: "Manage project webhooks",
        createLabelKey: "pm.add_webhook",
        createLabelFallback: "Add Webhook",
        moduleKey: "project_management",
        dashboardHref: "/dashboard/modules/project-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listPmWebhooks<Webhook>(params),
        createFn: createPmWebhook,
        updateFn: (id: number, p: Record<string, unknown>) => updatePmWebhook(id, p),
        deleteFn: deletePmWebhook,
        toForm: (row) => ({
          url: row.url ?? "", events: row.events ?? "", is_active: row.is_active ? "1" : "0",
        }),
        fromForm: (form) => ({
          url: form.url, events: form.events, is_active: form.is_active === "1",
          secret: form.secret || undefined,
        }),
      }}
    />
  );
}
