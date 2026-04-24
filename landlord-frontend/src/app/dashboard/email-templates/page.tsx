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
import { listEmailTemplates, fetchEmailTemplate, createEmailTemplate, updateEmailTemplate, deleteEmailTemplate, type EmailTemplateRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function EmailTemplatesPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<EmailTemplateRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ name: "", slug: "", subject: "", body: "" });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      setRows(await listEmailTemplates());
    } catch {
      setRows([]);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setForm({ name: "", slug: "", subject: "", body: "" });
    setSheetOpen(true);
  };

  const openEdit = async (id: number) => {
    setEditingId(id);
    setSheetOpen(true);
    try {
      const tpl = await fetchEmailTemplate(id);
      setForm({ name: tpl.name, slug: tpl.slug, subject: tpl.subject, body: tpl.body ?? "" });
    } catch {
      toast.error(t("dashboard.email_templates.toast_load_error", "Could not load template."));
      setSheetOpen(false);
    }
  };

  const save = async () => {
    setSaving(true);
    try {
      if (editingId == null) {
        await createEmailTemplate(form);
        toast.success(t("dashboard.email_templates.toast_created", "Template created."));
      } else {
        await updateEmailTemplate(editingId, form);
        toast.success(t("dashboard.email_templates.toast_updated", "Template updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.email_templates.toast_save_error", "Save failed."));
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
      await deleteEmailTemplate(deletingId);
      toast.success(t("dashboard.email_templates.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.email_templates.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<EmailTemplateRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "slug", header: t("dashboard.email_templates.slug", "Slug") },
      { accessorKey: "subject", header: t("dashboard.email_templates.subject", "Subject") },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => void openEdit(row.original.id)}>
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
        <h1 className="text-xl font-semibold">{t("dashboard.email_templates.title", "Email Templates")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.email_templates.subtitle", "Manage email templates for your campaigns.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.email_templates.create", "Create Template")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-xl">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.email_templates.sheet_create", "Create Template")
                : t("dashboard.email_templates.sheet_edit", "Edit Template")}
            </SheetTitle>
            <SheetDescription>{t("dashboard.email_templates.sheet_desc", "Fill in template details.")}</SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="tpl-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="tpl-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tpl-slug">{t("dashboard.email_templates.slug", "Slug")}</Label>
              <Input id="tpl-slug" value={form.slug} onChange={(e) => setForm((f) => ({ ...f, slug: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tpl-subject">{t("dashboard.email_templates.subject", "Subject")}</Label>
              <Input id="tpl-subject" value={form.subject} onChange={(e) => setForm((f) => ({ ...f, subject: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tpl-body">{t("dashboard.email_templates.body", "Body")}</Label>
              <textarea
                id="tpl-body"
                className="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={form.body}
                onChange={(e) => setForm((f) => ({ ...f, body: e.target.value }))}
              />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.email_templates.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.email_templates.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.email_templates.confirm_delete", "Delete this template?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
