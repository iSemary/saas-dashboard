"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listTmWebhooks, createTmWebhook, updateTmWebhook, deleteTmWebhook } from "@/lib/tm-resources";

type Webhook = { id: number; url: string; events: string; is_active: boolean; last_triggered_at: string };

const columns = (t: (k: string, f: string) => string): ColumnDef<Webhook>[] => [
  { accessorKey: "url", header: t("tm.url", "URL"), meta: { searchable: true } },
  { accessorKey: "events", header: t("tm.events", "Events") },
  { accessorKey: "is_active", header: t("tm.active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
  { accessorKey: "last_triggered_at", header: t("tm.last_triggered", "Last Triggered") },
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
        titleKey: "tm.webhooks",
        titleFallback: "Webhooks",
        subtitleKey: "tm.webhooks_subtitle",
        subtitleFallback: "Manage time management webhooks",
        createLabelKey: "tm.add_webhook",
        createLabelFallback: "Add Webhook",
        moduleKey: "time_management",
        dashboardHref: "/dashboard/modules/time-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listTmWebhooks<Webhook>(params),
        createFn: createTmWebhook,
        updateFn: (id: number, p: Record<string, unknown>) => updateTmWebhook(id, p),
        deleteFn: deleteTmWebhook,
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
