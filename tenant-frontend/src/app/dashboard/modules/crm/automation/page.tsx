"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listCrmAutomationRules,
  createCrmAutomationRule,
  updateCrmAutomationRule,
  deleteCrmAutomationRule,
} from "@/lib/tenant-resources";
import { Badge } from "@/components/ui/badge";

type AutomationRule = {
  id: number;
  name: string;
  trigger_type: string;
  action_type: string;
  is_active: boolean;
  created_at?: string;
};

const TRIGGER_LABELS: Record<string, string> = {
  lead_created: "Lead Created",
  lead_status_changed: "Lead Status Changed",
  lead_converted: "Lead Converted",
  opportunity_stage_changed: "Opportunity Stage Changed",
  opportunity_closed_won: "Deal Won",
  opportunity_closed_lost: "Deal Lost",
  activity_completed: "Activity Completed",
};

const ACTION_LABELS: Record<string, string> = {
  send_email: "Send Email",
  send_notification: "Send Notification",
  create_activity: "Create Activity",
  update_field: "Update Field",
  assign_user: "Assign User",
};

const config: SimpleCRUDConfig<AutomationRule> = {
  titleKey: "dashboard.crm.automation_rules",
  titleFallback: "Automation Rules",
  subtitleKey: "dashboard.crm.automation_rules_subtitle",
  subtitleFallback: "Automate your CRM workflows",
  createLabelKey: "dashboard.crm.add_rule",
  createLabelFallback: "Add Rule",
  fields: [
    { name: "name", label: "Rule Name", placeholder: "e.g., Send welcome email", required: true },
    {
      name: "trigger_type",
      label: "When (Trigger)",
      type: "select",
      required: true,
      options: [
        { value: "lead_created", label: "Lead Created" },
        { value: "lead_status_changed", label: "Lead Status Changed" },
        { value: "lead_converted", label: "Lead Converted" },
        { value: "opportunity_stage_changed", label: "Opportunity Stage Changed" },
        { value: "opportunity_closed_won", label: "Deal Won" },
        { value: "opportunity_closed_lost", label: "Deal Lost" },
        { value: "activity_completed", label: "Activity Completed" },
      ],
    },
    {
      name: "action_type",
      label: "Then (Action)",
      type: "select",
      required: true,
      options: [
        { value: "send_email", label: "Send Email" },
        { value: "send_notification", label: "Send Notification" },
        { value: "create_activity", label: "Create Activity" },
        { value: "update_field", label: "Update Field" },
        { value: "assign_user", label: "Assign User" },
      ],
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
  listFn: listCrmAutomationRules as () => Promise<AutomationRule[]>,
  createFn: createCrmAutomationRule,
  updateFn: updateCrmAutomationRule,
  deleteFn: deleteCrmAutomationRule as unknown as (id: number) => Promise<void>,
  moduleKey: "crm",
  dashboardHref: "/dashboard/modules/crm",
  columns: (t): Array<ColumnDef<AutomationRule>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.crm.rule_name", "Name") },
    {
      accessorKey: "trigger_type",
      header: t("dashboard.crm.trigger", "Trigger"),
      cell: ({ row }) => TRIGGER_LABELS[row.original.trigger_type] || row.original.trigger_type,
    },
    {
      accessorKey: "action_type",
      header: t("dashboard.crm.action", "Action"),
      cell: ({ row }) => ACTION_LABELS[row.original.action_type] || row.original.action_type,
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
    trigger_type: r.trigger_type ?? "",
    action_type: r.action_type ?? "",
    is_active: String(r.is_active ?? true),
  }),
  fromForm: (f) => ({
    name: f.name,
    trigger_type: f.trigger_type,
    action_type: f.action_type,
    is_active: f.is_active === "true",
  }),
};

export default function CrmAutomationPage() {
  return <SimpleCRUDPage config={config} />;
}
