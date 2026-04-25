"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Download, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";

type BackupRow = {
  id: number;
  name: string;
  size: string;
  created_at: string;
};

export default function BackupsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<BackupRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [creating, setCreating] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get("/backups");
      setRows(Array.isArray(res.data) ? (res.data as BackupRow[]) : []);
    } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const createBackup = async () => {
    setCreating(true);
    try {
      await api.post("/backups");
      toast.success(t("dashboard.backups.toast_created", "Backup created."));
      await load();
    } catch {
      toast.error(t("dashboard.backups.toast_create_error", "Could not create backup."));
    } finally {
      setCreating(false);
    }
  };

  const remove = async (id: number) => {
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await api.delete(`/backups/${deletingId}`);
      toast.success(t("dashboard.backups.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.backups.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<BackupRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "size", header: t("dashboard.backups.size", "Size") },
      {
        accessorKey: "created_at",
        header: t("dashboard.backups.created_at", "Created"),
        cell: ({ row }) => row.original.created_at ? new Date(row.original.created_at).toLocaleDateString() : "—",
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => { window.open(`${process.env.NEXT_PUBLIC_API_BASE_URL ?? "http://localhost:8000/api/landlord"}/backups/${row.original.id}/download`); }}>
              <Download className="size-3.5" />
            </Button>
            <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => void remove(row.original.id)}>
              <Trash2 className="size-3.5" />
            </Button>
          </div>
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
        <h1 className="text-xl font-semibold">{t("dashboard.backups.title", "Backups")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.backups.subtitle", "Create and manage database backups.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" disabled={creating} onClick={() => void createBackup()}>
            {creating ? <Loader2 className="size-4 animate-spin" /> : null}
            {t("dashboard.backups.create", "Create Backup")}
          </Button>
        )}
      />
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.backups.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.backups.confirm_delete", "Delete this backup?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
