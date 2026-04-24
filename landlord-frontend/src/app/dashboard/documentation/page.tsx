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
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";

type DocRow = {
  id: number;
  title: string;
  slug: string;
  category: string | null;
  is_published: boolean;
  created_at?: string;
};

export default function DocumentationPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<DocRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState({ title: "", slug: "", category: "", body: "", is_published: "0" });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get("/documentation");
      setRows(Array.isArray(res.data) ? (res.data as DocRow[]) : []);
    } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setForm({ title: "", slug: "", category: "", body: "", is_published: "0" });
    setSheetOpen(true);
  };

  const openEdit = async (id: number) => {
    setEditingId(id);
    setSheetOpen(true);
    try {
      const res = await api.get(`/documentation/${id}`);
      const doc = res.data as DocRow & { body?: string };
      setForm({ title: doc.title, slug: doc.slug, category: doc.category ?? "", body: (doc as { body?: string }).body ?? "", is_published: doc.is_published ? "1" : "0" });
    } catch {
      toast.error(t("dashboard.documentation.toast_load_error", "Could not load document."));
      setSheetOpen(false);
    }
  };

  const save = async () => {
    setSaving(true);
    try {
      const payload = { ...form, is_published: form.is_published === "1" };
      if (editingId == null) {
        await api.post("/documentation", payload);
        toast.success(t("dashboard.documentation.toast_created", "Document created."));
      } else {
        await api.put(`/documentation/${editingId}`, payload);
        toast.success(t("dashboard.documentation.toast_updated", "Document updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.documentation.toast_save_error", "Save failed."));
    } finally {
      setSaving(false);
    }
  };

  const remove = async (id: number) => {
    if (!window.confirm(t("dashboard.documentation.confirm_delete", "Delete this document?"))) return;
    try {
      await api.delete(`/documentation/${id}`);
      toast.success(t("dashboard.documentation.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.documentation.toast_delete_error", "Could not delete."));
    }
  };

  const columns: Array<ColumnDef<DocRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "title", header: t("dashboard.documentation.title_col", "Title") },
      { accessorKey: "slug", header: t("dashboard.documentation.slug", "Slug") },
      { accessorKey: "category", header: t("dashboard.documentation.category", "Category") },
      {
        accessorKey: "is_published",
        header: t("dashboard.documentation.published", "Published"),
        cell: ({ row }) => row.original.is_published ? "Yes" : "No",
      },
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
        <h1 className="text-xl font-semibold">{t("dashboard.documentation.title", "Documentation")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.documentation.subtitle", "Manage platform documentation and help articles.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.documentation.create", "Add Document")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-xl">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.documentation.sheet_create", "Add Document")
                : t("dashboard.documentation.sheet_edit", "Edit Document")}
            </SheetTitle>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="doc-title">{t("dashboard.documentation.title_col", "Title")}</Label>
              <Input id="doc-title" value={form.title} onChange={(e) => setForm((f) => ({ ...f, title: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="doc-slug">{t("dashboard.documentation.slug", "Slug")}</Label>
              <Input id="doc-slug" value={form.slug} onChange={(e) => setForm((f) => ({ ...f, slug: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="doc-category">{t("dashboard.documentation.category", "Category")}</Label>
              <Input id="doc-category" value={form.category} onChange={(e) => setForm((f) => ({ ...f, category: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="doc-body">{t("dashboard.documentation.body", "Body")}</Label>
              <textarea
                id="doc-body"
                className="flex min-h-[160px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={form.body}
                onChange={(e) => setForm((f) => ({ ...f, body: e.target.value }))}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="doc-published">{t("dashboard.documentation.published", "Published")}</Label>
              <select id="doc-published" className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm" value={form.is_published} onChange={(e) => setForm((f) => ({ ...f, is_published: e.target.value }))}>
                <option value="0">Draft</option>
                <option value="1">Published</option>
              </select>
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.documentation.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
    </div>
  );
}
