"use client";

import { useEffect, useState } from "react";
import { Loader2, RefreshCw } from "lucide-react";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { listLoginAttempts } from "@/lib/tenant-resources";
import { DataTable } from "@/components/data-table";
import { ColumnDef } from "@tanstack/react-table";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

type Attempt = { id: number; email?: string; ip?: string; successful?: boolean; created_at: string };

export default function LoginAttemptsPage() {
  const { t } = useI18n();
  const [attempts, setAttempts] = useState<Attempt[]>([]);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    try { setAttempts(await listLoginAttempts() as Attempt[]); } catch { toast.error(t("dashboard.crud.load_error", "Failed to load.")); }
    finally { setLoading(false); }
  };

  useEffect(() => { void load(); }, []);

  const columns: Array<ColumnDef<Attempt>> = [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "email", header: t("dashboard.table.email", "Email") },
    { accessorKey: "ip", header: t("dashboard.table.ip", "IP") },
    { accessorKey: "successful", header: t("dashboard.table.status", "Status"), cell: ({ row }) => <Badge variant={row.original.successful ? "default" : "destructive"}>{row.original.successful ? "Success" : "Failed"}</Badge> },
    { accessorKey: "created_at", header: t("dashboard.table.date", "Date") },
  ];

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-xl font-semibold">{t("dashboard.login_attempts.title", "Login Attempts")}</h1>
            <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.login_attempts.subtitle", "Recent login attempts")}</p>
          </div>
          <Button type="button" variant="outline" size="sm" onClick={() => void load()}><RefreshCw className="size-4" /></Button>
        </div>
      </div>
      <DataTable columns={columns} data={attempts} />
    </div>
  );
}
