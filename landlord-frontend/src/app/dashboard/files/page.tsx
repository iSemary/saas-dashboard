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
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";

type FileRow = {
  id: number;
  name: string;
  path: string;
  size: string;
  mime_type: string;
  created_at?: string;
};

export default function FilesPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<FileRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ name: "", path: "" });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get("/files");
      setRows(Array.isArray(res.data) ? (res.data as FileRow[]) : []);
    } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const openCreate = () => {
    setForm({ name: "", path: "" });
    setSheetOpen(true);
  };

  const save = async () => {
    setSaving(true);
    try {
      await api.post("/files", form);
      toast.success(t("dashboard.files.toast_created", "File uploaded."));
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.files.toast_save_error", "Upload failed."));
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
      await api.delete(`/files/${deletingId}`);
      toast.success(t("dashboard.files.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.files.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<FileRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "path", header: t("dashboard.files.path", "Path") },
      { accessorKey: "size", header: t("dashboard.files.size", "Size") },
      { accessorKey: "mime_type", header: t("dashboard.files.mime_type", "Type") },
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
        <h1 className="text-xl font-semibold">{t("dashboard.files.title", "Files")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.files.subtitle", "Manage uploaded files.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.files.upload", "Upload")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>{t("dashboard.files.sheet_upload", "Upload File")}</SheetTitle>
            <SheetDescription>{t("dashboard.files.sheet_desc", "Add a new file.")}</SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="file-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="file-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="file-path">{t("dashboard.files.path", "Path")}</Label>
              <Input id="file-path" value={form.path} onChange={(e) => setForm((f) => ({ ...f, path: e.target.value }))} />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.files.upload", "Upload")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.files.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.files.confirm_delete", "Delete this file?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
