"use client";

import { useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2 } from "lucide-react";
import { DataTable } from "@/components/data-table";
import { listActivityLogs } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

type ActivityLogRow = {
  id: number;
  description: string;
  subject_type?: string;
  subject_id?: number;
  causer_type?: string;
  causer_id?: number;
  created_at: string;
};

export default function ActivityLogsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<ActivityLogRow[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    listActivityLogs()
      .then((data) => setRows(data as ActivityLogRow[]))
      .catch(() => setRows([]))
      .finally(() => setLoading(false));
  }, []);

  const columns: Array<ColumnDef<ActivityLogRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "description", header: t("dashboard.activity_logs.description", "Description") },
      {
        accessorKey: "created_at",
        header: t("dashboard.activity_logs.created_at", "Date"),
        cell: ({ row }) => row.original.created_at ? new Date(row.original.created_at).toLocaleString() : "—",
      },
    ],
    [t],
  );

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.activity_logs.title", "Activity Logs")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.activity_logs.subtitle", "View audit trail of system actions.")}
        </p>
      </div>
      <DataTable columns={columns} data={rows} />
    </div>
  );
}
