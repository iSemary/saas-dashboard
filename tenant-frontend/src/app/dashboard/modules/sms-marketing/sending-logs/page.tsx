"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmSendingLogs, type SmSendingLog } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmSendingLog>[] => [
  { accessorKey: "campaign_id", header: t("sms_marketing.campaign_id", "Campaign ID") },
  { accessorKey: "contact_id", header: t("sms_marketing.contact_id", "Contact ID") },
  { accessorKey: "status", header: t("sms_marketing.status", "Status") },
  { accessorKey: "sent_at", header: t("sms_marketing.sent_at", "Sent At") },
  { accessorKey: "delivered_at", header: t("sms_marketing.delivered_at", "Delivered At") },
  { accessorKey: "failed_reason", header: t("sms_marketing.failed_reason", "Failed Reason") },
  { accessorKey: "cost", header: t("sms_marketing.cost", "Cost") },
];

export default function SmSendingLogsPage() {
  return (
    <SimpleCRUDPage<SmSendingLog>
      config={{
        titleKey: "sms_marketing.sending_logs",
        titleFallback: "Sending Logs",
        subtitleKey: "sms_marketing.sending_logs_subtitle",
        subtitleFallback: "View sending logs (read-only)",
        createLabelKey: "sms_marketing.create",
        createLabelFallback: "View",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields: [],
        columns,
        listFn: (params?: TableParams) => getSmSendingLogs(params),
        createFn: async () => {},
        updateFn: null,
        deleteFn: null,
        toForm: () => ({}),
        fromForm: () => ({}),
      }}
    />
  );
}
