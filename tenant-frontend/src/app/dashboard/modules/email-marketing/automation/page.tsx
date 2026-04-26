"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmAutomationRules, createEmAutomationRule, updateEmAutomationRule, deleteEmAutomationRule, type EmAutomationRule } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmAutomationRule>[] => [
  { accessorKey: "name", header: t("email_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "trigger_type", header: t("email_marketing.trigger_type", "Trigger") },
  { accessorKey: "action_type", header: t("email_marketing.action_type", "Action") },
  { accessorKey: "is_active", header: t("email_marketing.is_active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "trigger_type", label: "Trigger Type", type: "select", required: true, options: [
    { value: "contact_added", label: "Contact Added" }, { value: "campaign_sent", label: "Campaign Sent" },
    { value: "email_opened", label: "Email Opened" }, { value: "email_clicked", label: "Email Clicked" },
    { value: "unsubscribed", label: "Unsubscribed" },
  ]},
  { name: "action_type", label: "Action Type", type: "select", options: [
    { value: "send_campaign", label: "Send Campaign" }, { value: "add_to_list", label: "Add to List" },
    { value: "remove_from_list", label: "Remove from List" }, { value: "webhook", label: "Webhook" },
  ]},
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
];

export default function EmAutomationPage() {
  return (
    <SimpleCRUDPage<EmAutomationRule>
      config={{
        titleKey: "email_marketing.automation",
        titleFallback: "Automation Rules",
        subtitleKey: "email_marketing.automation_subtitle",
        subtitleFallback: "Manage automation rules",
        createLabelKey: "email_marketing.add_rule",
        createLabelFallback: "Add Rule",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmAutomationRules(params),
        createFn: createEmAutomationRule,
        updateFn: updateEmAutomationRule,
        deleteFn: deleteEmAutomationRule,
        toForm: (row) => ({
          name: row.name ?? "", trigger_type: row.trigger_type ?? "contact_added",
          action_type: row.action_type ?? "send_campaign", is_active: row.is_active ? "1" : "0",
        }),
        fromForm: (form) => ({
          name: form.name, trigger_type: form.trigger_type,
          action_type: form.action_type || undefined, is_active: form.is_active === "1",
        }),
      }}
    />
  );
}
