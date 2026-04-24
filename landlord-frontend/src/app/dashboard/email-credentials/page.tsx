"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2 } from "lucide-react";
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
import { listEmailCredentials, createEmailCredential, updateEmailCredential, deleteEmailCredential, type EmailCredentialRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function EmailCredentialsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<EmailCredentialRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ name: "", host: "", port: "587", username: "", password: "", encryption: "tls" });

  const load = useCallback(async () => {
    setLoading(true);
    try { setRows(await listEmailCredentials()); } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setForm({ name: "", host: "", port: "587", username: "", password: "", encryption: "tls" });
    setSheetOpen(true);
  };

  const openEdit = (row: EmailCredentialRow) => {
    setEditingId(row.id);
    setForm({ name: row.name, host: row.host, port: String(row.port), username: row.username, password: "", encryption: row.encryption });
    setSheetOpen(true);
  };

  const save = async () => {
    setSaving(true);
    try {
      const payload = { ...form, port: Number(form.port) };
      if (editingId == null) {
        await createEmailCredential(payload);
        toast.success(t("dashboard.email_credentials.toast_created", "Credential created."));
      } else {
        await updateEmailCredential(editingId, payload);
        toast.success(t("dashboard.email_credentials.toast_updated", "Credential updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.email_credentials.toast_save_error", "Save failed."));
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
      await deleteEmailCredential(deletingId);
      toast.success(t("dashboard.email_credentials.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.email_credentials.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<EmailCredentialRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "host", header: t("dashboard.email_credentials.host", "Host") },
      { accessorKey: "username", header: t("dashboard.email_credentials.username", "Username") },
      { accessorKey: "encryption", header: t("dashboard.email_credentials.encryption", "Encryption") },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => openEdit(row.original)}>
              <Pencil className="size-3.5" />
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
        <h1 className="text-xl font-semibold">{t("dashboard.email_credentials.title", "Email Credentials")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.email_credentials.subtitle", "Manage SMTP credentials for sending emails.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.email_credentials.create", "Add Credential")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.email_credentials.sheet_create", "Add Credential")
                : t("dashboard.email_credentials.sheet_edit", "Edit Credential")}
            </SheetTitle>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="ec-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="ec-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ec-host">{t("dashboard.email_credentials.host", "Host")}</Label>
              <Input id="ec-host" value={form.host} onChange={(e) => setForm((f) => ({ ...f, host: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ec-port">Port</Label>
              <Input id="ec-port" type="number" value={form.port} onChange={(e) => setForm((f) => ({ ...f, port: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ec-username">{t("dashboard.email_credentials.username", "Username")}</Label>
              <Input id="ec-username" value={form.username} onChange={(e) => setForm((f) => ({ ...f, username: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ec-password">{t("dashboard.auth.password", "Password")}</Label>
              <Input id="ec-password" type="password" value={form.password} onChange={(e) => setForm((f) => ({ ...f, password: e.target.value }))} placeholder={editingId ? t("dashboard.email_credentials.leave_unchanged", "Leave unchanged") : ""} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ec-encryption">{t("dashboard.email_credentials.encryption", "Encryption")}</Label>
              <select id="ec-encryption" className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm" value={form.encryption} onChange={(e) => setForm((f) => ({ ...f, encryption: e.target.value }))}>
                <option value="tls">TLS</option>
                <option value="ssl">SSL</option>
                <option value="none">None</option>
              </select>
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.email_credentials.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.email_credentials.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.email_credentials.confirm_delete", "Delete this credential?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
