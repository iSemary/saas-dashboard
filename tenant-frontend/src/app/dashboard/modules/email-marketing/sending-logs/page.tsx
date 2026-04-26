"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmSendingLogs, type EmSendingLog } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmSendingLog>[] => [
  { accessorKey: "campaign_id", header: t("email_marketing.campaign_id", "Campaign ID") },
  { accessorKey: "contact_id", header: t("email_marketing.contact_id", "Contact ID") },
  { accessorKey: "status", header: t("email_marketing.status", "Status") },
  { accessorKey: "sent_at", header: t("email_marketing.sent_at", "Sent At") },
  { accessorKey: "opened_at", header: t("email_marketing.opened_at", "Opened At") },
  { accessorKey: "clicked_at", header: t("email_marketing.clicked_at", "Clicked At") },
  { accessorKey: "failed_reason", header: t("email_marketing.failed_reason", "Failed Reason") },
];

export default function EmSendingLogsPage() {
  return (
    <SimpleCRUDPage<EmSendingLog>
      config={{
        titleKey: "email_marketing.sending_logs",
        titleFallback: "Sending Logs",
        subtitleKey: "email_marketing.sending_logs_subtitle",
        subtitleFallback: "View sending logs (read-only)",
        createLabelKey: "email_marketing.create",
        createLabelFallback: "View",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields: [],
        columns,
        listFn: (params?: TableParams) => getEmSendingLogs(params),
        createFn: async () => {},
        updateFn: null,
        deleteFn: null,
        toForm: () => ({}),
        fromForm: () => ({}),
      }}
    />
  );
}
