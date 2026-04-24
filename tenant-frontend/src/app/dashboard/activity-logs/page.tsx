"use client";

import { useEffect, useState } from "react";
import { Loader2, RefreshCw } from "lucide-react";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { listActivityLogs } from "@/lib/tenant-resources";
import { DataTable } from "@/components/data-table";
import { ColumnDef } from "@tanstack/react-table";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

type Log = { id: number; description: string; causer?: { name: string }; created_at: string };

export default function ActivityLogsPage() {
  const { t } = useI18n();
  const [logs, setLogs] = useState<Log[]>([]);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    try { setLogs(await listActivityLogs() as Log[]); } catch { toast.error(t("dashboard.crud.load_error", "Failed to load.")); }
    finally { setLoading(false); }
  };

  useEffect(() => { void load(); }, []);

  const columns: Array<ColumnDef<Log>> = [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "description", header: t("dashboard.table.description", "Description") },
    { accessorKey: "causer", header: t("dashboard.table.causer", "Causer"), cell: ({ row }) => row.original.causer?.name ?? "—" },
    { accessorKey: "created_at", header: t("dashboard.table.date", "Date") },
  ];

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-xl font-semibold">{t("dashboard.activity_logs.title", "Activity Logs")}</h1>
            <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.activity_logs.subtitle", "Track user actions")}</p>
          </div>
          <Button type="button" variant="outline" size="sm" onClick={() => void load()}><RefreshCw className="size-4" /></Button>
        </div>
      </div>
      <DataTable columns={columns} data={logs} />
    </div>
  );
}
