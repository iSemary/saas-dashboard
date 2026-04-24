"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listEmailSubscribers, createEmailSubscriber, deleteEmailSubscriber, type EmailSubscriberRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function EmailSubscribersPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<EmailSubscriberRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ email: "", name: "" });

  const load = useCallback(async () => {
    setLoading(true);
    try { setRows(await listEmailSubscribers()); } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const save = async () => {
    if (!form.email.trim()) return;
    setSaving(true);
    try {
      await createEmailSubscriber(form);
      toast.success(t("dashboard.email_subscribers.toast_created", "Subscriber added."));
      setSheetOpen(false);
      setForm({ email: "", name: "" });
      await load();
    } catch {
      toast.error(t("dashboard.email_subscribers.toast_save_error", "Could not add subscriber."));
    } finally {
      setSaving(false);
    }
  };

  const remove = async (id: number) => {
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await deleteEmailSubscriber(deletingId);
      toast.success(t("dashboard.email_subscribers.toast_deleted", "Removed."));
      await load();
    } catch {
      toast.error(t("dashboard.email_subscribers.toast_delete_error", "Could not remove."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<EmailSubscriberRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "email", header: t("dashboard.users.col_email", "Email") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      {
        accessorKey: "status",
        header: t("dashboard.email_subscribers.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.status === "active" ? "default" : "secondary"}>
            {row.original.status}
          </Badge>
        ),
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
        <h1 className="text-xl font-semibold">{t("dashboard.email_subscribers.title", "Email Subscribers")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.email_subscribers.subtitle", "Manage newsletter subscribers.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={() => { setForm({ email: "", name: "" }); setSheetOpen(true); }}>
            <Plus className="size-4" />
            {t("dashboard.email_subscribers.create", "Add Subscriber")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>{t("dashboard.email_subscribers.sheet_create", "Add Subscriber")}</SheetTitle>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="es-email">{t("dashboard.users.col_email", "Email")}</Label>
              <Input id="es-email" type="email" value={form.email} onChange={(e) => setForm((f) => ({ ...f, email: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="es-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="es-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.email_subscribers.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.email_subscribers.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.email_subscribers.confirm_delete", "Remove this subscriber?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
