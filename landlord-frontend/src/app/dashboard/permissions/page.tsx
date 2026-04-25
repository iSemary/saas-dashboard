"use client";

import { useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { DataTable } from "@/components/data-table";
import { listPermissions } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

type PermissionRow = { id: number; name: string; guard_name?: string };

export default function PermissionsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<PermissionRow[]>([]);
  useEffect(() => {
    listPermissions()
      .then((permissions) => setRows(permissions as PermissionRow[]))
      .catch(() => setRows([]));
  }, []);
  const columns = useMemo<Array<ColumnDef<PermissionRow>>>(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "guard_name", header: t("dashboard.permissions.guard_name", "Guard") },
    ],
    [t],
  );
  return (
    <div className="space-y-3">
      <h1 className="text-xl font-semibold">{t("dashboard.permissions.page_title", "Permissions")}</h1>
      <p className="text-sm text-muted-foreground">
        {t("dashboard.permissions.subtitle", "All registered permissions in the system.")}
      </p>
      <DataTable columns={columns} data={rows} />
    </div>
  );
}
