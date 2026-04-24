"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { SlugInput } from "@/components/ui/slug-input";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listFeatureFlags } from "@/lib/resources";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";

type FeatureFlagRow = {
  id: number;
  name: string;
  slug: string;
  description: string | null;
  is_active: boolean;
  created_at?: string;
};

export default function FeatureFlagsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<FeatureFlagRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ name: "", slug: "", description: "", is_active: "1" });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get("/feature-flags");
      setRows(Array.isArray(res.data) ? (res.data as FeatureFlagRow[]) : []);
    } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setForm({ name: "", slug: "", description: "", is_active: "1" });
    setSheetOpen(true);
  };

  const openEdit = (row: FeatureFlagRow) => {
    setEditingId(row.id);
    setForm({ name: row.name, slug: row.slug, description: row.description ?? "", is_active: row.is_active ? "1" : "0" });
    setSheetOpen(true);
  };

  const save = async () => {
    setSaving(true);
    try {
      const payload = { ...form, is_active: form.is_active === "1" };
      if (editingId == null) {
        await api.post("/feature-flags", payload);
        toast.success(t("dashboard.feature_flags.toast_created", "Feature flag created."));
      } else {
        await api.put(`/feature-flags/${editingId}`, payload);
        toast.success(t("dashboard.feature_flags.toast_updated", "Feature flag updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.feature_flags.toast_save_error", "Save failed."));
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
      await api.delete(`/feature-flags/${deletingId}`);
      toast.success(t("dashboard.feature_flags.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.feature_flags.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<FeatureFlagRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "slug", header: t("dashboard.feature_flags.slug", "Slug") },
      {
        accessorKey: "is_active",
        header: t("dashboard.feature_flags.status", "Status"),
        cell: ({ row }) => row.original.is_active ? "Active" : "Inactive",
      },
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
        <h1 className="text-xl font-semibold">{t("dashboard.feature_flags.title", "Feature Flags")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.feature_flags.subtitle", "Toggle features on or off across the platform.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.feature_flags.create", "Add Flag")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.feature_flags.sheet_create", "Add Flag")
                : t("dashboard.feature_flags.sheet_edit", "Edit Flag")}
            </SheetTitle>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="ff-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="ff-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ff-slug">{t("dashboard.feature_flags.slug", "Slug")}</Label>
              <SlugInput id="ff-slug" value={form.slug} onChange={(v) => setForm((f) => ({ ...f, slug: v }))} sourceValue={form.name} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ff-desc">{t("dashboard.feature_flags.description", "Description")}</Label>
              <Input id="ff-desc" value={form.description} onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="ff-active">{t("dashboard.feature_flags.status", "Status")}</Label>
              <select id="ff-active" className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm" value={form.is_active} onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.value }))}>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.feature_flags.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.feature_flags.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.feature_flags.confirm_delete", "Delete this flag?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
