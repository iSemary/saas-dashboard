"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmAutomationRules, createSmAutomationRule, updateSmAutomationRule, deleteSmAutomationRule, type SmAutomationRule } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmAutomationRule>[] => [
  { accessorKey: "name", header: t("sms_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "trigger_type", header: t("sms_marketing.trigger_type", "Trigger") },
  { accessorKey: "action_type", header: t("sms_marketing.action_type", "Action") },
  { accessorKey: "is_active", header: t("sms_marketing.is_active", "Active"), cell: ({ row }) => row.original.is_active ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "trigger_type", label: "Trigger Type", type: "select", required: true, options: [
    { value: "contact_added", label: "Contact Added" }, { value: "sms_sent", label: "SMS Sent" },
    { value: "sms_delivered", label: "SMS Delivered" }, { value: "sms_failed", label: "SMS Failed" },
    { value: "opted_out", label: "Opted Out" },
  ]},
  { name: "action_type", label: "Action Type", type: "select", options: [
    { value: "send_campaign", label: "Send Campaign" }, { value: "add_to_list", label: "Add to List" },
    { value: "remove_from_list", label: "Remove from List" }, { value: "webhook", label: "Webhook" },
  ]},
  { name: "is_active", label: "Active", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
];

export default function SmAutomationPage() {
  return (
    <SimpleCRUDPage<SmAutomationRule>
      config={{
        titleKey: "sms_marketing.automation",
        titleFallback: "Automation Rules",
        subtitleKey: "sms_marketing.automation_subtitle",
        subtitleFallback: "Manage automation rules",
        createLabelKey: "sms_marketing.add_rule",
        createLabelFallback: "Add Rule",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmAutomationRules(params),
        createFn: createSmAutomationRule,
        updateFn: updateSmAutomationRule,
        deleteFn: deleteSmAutomationRule,
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
