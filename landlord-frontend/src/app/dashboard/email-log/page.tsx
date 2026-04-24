"use client";

import { useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listEmailLog, deleteEmailLog, type EmailLogRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function EmailLogPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<EmailLogRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);

  useEffect(() => {
    listEmailLog()
      .then(setRows)
      .catch(() => setRows([]))
      .finally(() => setLoading(false));
  }, []);

  const remove = async (id: number) => {
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await deleteEmailLog(deletingId);
      toast.success(t("dashboard.email_log.toast_deleted", "Deleted."));
      setRows((r) => r.filter((x) => x.id !== deletingId));
    } catch {
      toast.error(t("dashboard.email_log.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<EmailLogRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "template", header: t("dashboard.email_log.template", "Template") },
      { accessorKey: "email", header: t("dashboard.users.col_email", "Email") },
      {
        accessorKey: "status",
        header: t("dashboard.email_log.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.status === "sent" ? "default" : "secondary"}>
            {row.original.status}
          </Badge>
        ),
      },
      {
        accessorKey: "created_at",
        header: t("dashboard.email_log.created_at", "Created"),
        cell: ({ row }) => row.original.created_at ? new Date(row.original.created_at).toLocaleDateString() : "—",
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => void remove(row.original.id)}>
            <Trash2 className="size-3.5" />
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
        <h1 className="text-xl font-semibold">{t("dashboard.email_log.title", "Email Log")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.email_log.subtitle", "View sent email history.")}
        </p>
      </div>
      <DataTable columns={columns} data={rows} />
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.email_log.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.email_log.confirm_delete", "Delete this email log?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
