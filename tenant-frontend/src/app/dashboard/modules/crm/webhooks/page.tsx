"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listCrmWebhooks,
  createCrmWebhook,
  updateCrmWebhook,
  deleteCrmWebhook,
} from "@/lib/tenant-resources";
import { Badge } from "@/components/ui/badge";

type Webhook = {
  id: number;
  name: string;
  url: string;
  events: string[];
  is_active: boolean;
  created_at?: string;
};

const WEBHOOK_EVENTS = [
  { value: "lead.created", label: "Lead Created" },
  { value: "lead.updated", label: "Lead Updated" },
  { value: "lead.converted", label: "Lead Converted" },
  { value: "contact.created", label: "Contact Created" },
  { value: "contact.updated", label: "Contact Updated" },
  { value: "company.created", label: "Company Created" },
  { value: "company.updated", label: "Company Updated" },
  { value: "opportunity.created", label: "Opportunity Created" },
  { value: "opportunity.stage_changed", label: "Opportunity Stage Changed" },
  { value: "opportunity.closed", label: "Opportunity Closed" },
  { value: "activity.completed", label: "Activity Completed" },
];

const config: SimpleCRUDConfig<Webhook> = {
  titleKey: "dashboard.crm.webhooks",
  titleFallback: "Webhooks",
  subtitleKey: "dashboard.crm.webhooks_subtitle",
  subtitleFallback: "Configure webhooks for CRM events",
  createLabelKey: "dashboard.crm.add_webhook",
  createLabelFallback: "Add Webhook",
  fields: [
    { name: "name", label: "Webhook Name", placeholder: "e.g., Slack Integration", required: true },
    { name: "url", label: "Webhook URL", type: "url", placeholder: "https://api.example.com/webhook", required: true },
    {
      name: "events",
      label: "Events",
      type: "select",
      required: true,
      options: WEBHOOK_EVENTS,
    },
    {
      name: "is_active",
      label: "Status",
      type: "select",
      options: [
        { value: "true", label: "Active" },
        { value: "false", label: "Inactive" },
      ],
    },
  ],
  listFn: listCrmWebhooks as () => Promise<Webhook[]>,
  createFn: createCrmWebhook,
  updateFn: updateCrmWebhook,
  deleteFn: deleteCrmWebhook as unknown as (id: number) => Promise<void>,
  moduleKey: "crm",
  dashboardHref: "/dashboard/modules/crm",
  columns: (t): Array<ColumnDef<Webhook>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.crm.webhook_name", "Name") },
    { accessorKey: "url", header: t("dashboard.crm.webhook_url", "URL"), cell: ({ row }) => <span className="truncate max-w-[200px] block">{row.original.url}</span> },
    {
      accessorKey: "events",
      header: t("dashboard.crm.events", "Events"),
      cell: ({ row }) => (
        <div className="flex flex-wrap gap-1">
          {row.original.events?.slice(0, 2).map((e) => (
            <Badge key={e} variant="outline" className="text-xs">{e}</Badge>
          ))}
          {row.original.events && row.original.events.length > 2 && (
            <Badge variant="outline" className="text-xs">+{row.original.events.length - 2}</Badge>
          )}
        </div>
      ),
    },
    {
      accessorKey: "is_active",
      header: t("dashboard.table.status", "Status"),
      cell: ({ row }) => (
        <Badge variant={row.original.is_active ? "default" : "secondary"}>
          {row.original.is_active ? "Active" : "Inactive"}
        </Badge>
      ),
    },
  ],
  toForm: (r) => ({
    name: r.name ?? "",
    url: r.url ?? "",
    events: r.events?.[0] ?? "",
    is_active: String(r.is_active ?? true),
  }),
  fromForm: (f) => ({
    name: f.name,
    url: f.url,
    events: [f.events],
    is_active: f.is_active === "true",
  }),
};

export default function CrmWebhooksPage() {
  return <SimpleCRUDPage config={config} />;
}
