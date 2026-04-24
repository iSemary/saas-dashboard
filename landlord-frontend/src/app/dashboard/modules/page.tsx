"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { listModules, toggleModule, type ModuleRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function ModulesPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<ModuleRow[]>([]);
  const [loading, setLoading] = useState(true);

  const load = useCallback(async () => {
    setLoading(true);
    try { setRows(await listModules()); } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const handleToggle = async (row: ModuleRow) => {
    try {
      await toggleModule(row.id, !row.is_active);
      toast.success(row.is_active
        ? t("dashboard.modules.deactivated", "Module deactivated.")
        : t("dashboard.modules.activated", "Module activated.")
      );
      await load();
    } catch {
      toast.error(t("dashboard.modules.toggle_error", "Could not toggle module."));
    }
  };

  const columns: Array<ColumnDef<ModuleRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "slug", header: t("dashboard.modules.slug", "Slug") },
      {
        accessorKey: "is_active",
        header: t("dashboard.modules.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.is_active ? "default" : "secondary"}>
            {row.original.is_active ? "Active" : "Inactive"}
          </Badge>
        ),
      },
      { accessorKey: "version", header: t("dashboard.modules.version", "Version") },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => void handleToggle(row.original)}>
            {row.original.is_active
              ? t("dashboard.modules.deactivate", "Deactivate")
              : t("dashboard.modules.activate", "Activate")}
          </Button>
        ),
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
        <h1 className="text-xl font-semibold">{t("dashboard.modules.title", "Modules")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.modules.subtitle", "Enable or disable platform modules.")}
        </p>
      </div>
      <DataTable columns={columns} data={rows} />
    </div>
  );
}
