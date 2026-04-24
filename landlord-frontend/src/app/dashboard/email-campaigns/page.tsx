"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Trash2, Send } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listEmailCampaigns, deleteEmailCampaign, type EmailCampaignRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function EmailCampaignsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<EmailCampaignRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    try { setRows(await listEmailCampaigns()); } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const remove = async (id: number) => {
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await deleteEmailCampaign(deletingId);
      toast.success(t("dashboard.email_campaigns.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.email_campaigns.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<EmailCampaignRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "subject", header: t("dashboard.email_campaigns.subject", "Subject") },
      {
        accessorKey: "status",
        header: t("dashboard.email_campaigns.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.status === "sent" ? "default" : row.original.status === "draft" ? "secondary" : "outline"}>
            {row.original.status}
          </Badge>
        ),
      },
      {
        accessorKey: "created_at",
        header: t("dashboard.email_campaigns.created_at", "Created"),
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
        <h1 className="text-xl font-semibold">{t("dashboard.email_campaigns.title", "Email Campaigns")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.email_campaigns.subtitle", "Manage email marketing campaigns.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={() => { window.location.href = "/dashboard/compose-email"; }}>
            <Send className="size-4" />
            {t("dashboard.email_campaigns.compose", "Compose Email")}
          </Button>
        )}
      />
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.email_campaigns.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.email_campaigns.confirm_delete", "Delete this campaign?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
