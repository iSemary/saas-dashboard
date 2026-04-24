"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listSubscriptions, deleteSubscription, type SubscriptionRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function SubscriptionsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<SubscriptionRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try { setRows(await listSubscriptions()); } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const remove = async (id: number) => {
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await deleteSubscription(deletingId);
      toast.success(t("dashboard.subscriptions.toast_deleted", "Subscription cancelled."));
      await load();
    } catch {
      toast.error(t("dashboard.subscriptions.toast_delete_error", "Could not cancel."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<SubscriptionRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      {
        id: "tenant",
        header: t("dashboard.subscriptions.tenant", "Tenant"),
        cell: ({ row }) => row.original.tenant?.name ?? "—",
      },
      {
        id: "plan",
        header: t("dashboard.subscriptions.plan", "Plan"),
        cell: ({ row }) => row.original.plan?.name ?? "—",
      },
      {
        accessorKey: "status",
        header: t("dashboard.subscriptions.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.status === "active" ? "default" : "secondary"}>
            {row.original.status}
          </Badge>
        ),
      },
      {
        accessorKey: "expires_at",
        header: t("dashboard.subscriptions.expires", "Expires"),
        cell: ({ row }) => row.original.expires_at ? new Date(row.original.expires_at).toLocaleDateString() : "—",
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
        <h1 className="text-xl font-semibold">{t("dashboard.subscriptions.title", "Subscriptions")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.subscriptions.subtitle", "View and manage tenant subscriptions.")}
        </p>
      </div>
      <DataTable columns={columns} data={rows} />
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.subscriptions.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.subscriptions.confirm_delete", "Cancel this subscription?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
