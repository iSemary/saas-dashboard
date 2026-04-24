"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listEmailRecipients, createEmailRecipient, deleteEmailRecipient, type EmailRecipientRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function EmailRecipientsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<EmailRecipientRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ name: "", email: "" });

  const load = useCallback(async () => {
    setLoading(true);
    try { setRows(await listEmailRecipients()); } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const save = async () => {
    setSaving(true);
    try {
      await createEmailRecipient(form);
      toast.success(t("dashboard.email_recipients.toast_created", "Recipient added."));
      setSheetOpen(false);
      setForm({ name: "", email: "" });
      await load();
    } catch {
      toast.error(t("dashboard.email_recipients.toast_save_error", "Could not add recipient."));
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
      await deleteEmailRecipient(deletingId);
      toast.success(t("dashboard.email_recipients.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.email_recipients.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<EmailRecipientRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "email", header: t("dashboard.users.col_email", "Email") },
      {
        accessorKey: "group",
        header: t("dashboard.email_recipients.group", "Group"),
        cell: ({ row }) => row.original.group?.name ?? "—",
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
        <h1 className="text-xl font-semibold">{t("dashboard.email_recipients.title", "Email Recipients")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.email_recipients.subtitle", "Manage email recipients.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={() => { setForm({ name: "", email: "" }); setSheetOpen(true); }}>
            <Plus className="size-4" />
            {t("dashboard.email_recipients.create", "Add Recipient")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>{t("dashboard.email_recipients.sheet_create", "Add Recipient")}</SheetTitle>
            <SheetDescription>{t("dashboard.email_recipients.sheet_desc", "Enter recipient details.")}</SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="er-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="er-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="er-email">{t("dashboard.users.col_email", "Email")}</Label>
              <Input id="er-email" type="email" value={form.email} onChange={(e) => setForm((f) => ({ ...f, email: e.target.value }))} />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.email_recipients.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.email_recipients.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.email_recipients.confirm_delete", "Delete this recipient?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
