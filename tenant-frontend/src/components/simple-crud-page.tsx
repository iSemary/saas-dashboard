"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { RichTextEditor } from "@/components/ui/rich-text-editor";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { useI18n } from "@/context/i18n-context";

export type FieldDef = {
  name: string;
  label: string;
  type?: "text" | "email" | "password" | "number" | "url" | "textarea" | "select" | "richtext";
  placeholder?: string;
  required?: boolean;
  options?: Array<{ value: string; label: string }>;
};

export type SimpleCRUDConfig<T extends { id: number }> = {
  titleKey: string;
  titleFallback: string;
  subtitleKey: string;
  subtitleFallback: string;
  createLabelKey: string;
  createLabelFallback: string;
  fields: FieldDef[];
  listFn: () => Promise<T[]>;
  createFn: (payload: Record<string, unknown>) => Promise<unknown>;
  updateFn?: ((id: number, payload: Record<string, unknown>) => Promise<unknown>) | null;
  deleteFn?: ((id: number) => Promise<void>) | null;
  columns: (t: (key: string, fallback: string) => string) => Array<ColumnDef<T>>;
  toForm: (row: T) => Record<string, string>;
  fromForm: (form: Record<string, string>) => Record<string, unknown>;
};

export function SimpleCRUDPage<T extends { id: number }>({
  config,
}: {
  config: SimpleCRUDConfig<T>;
}) {
  const { t } = useI18n();
  const [rows, setRows] = useState<T[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState<Record<string, string>>(() => {
    const init: Record<string, string> = {};
    for (const f of config.fields) init[f.name] = "";
    return init;
  });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      setRows(await config.listFn());
    } catch {
      setRows([]);
    } finally {
      setLoading(false);
    }
  }, [config]);

  useEffect(() => {
    void load();
  }, [load]);

  const openCreate = () => {
    setEditingId(null);
    const init: Record<string, string> = {};
    for (const f of config.fields) init[f.name] = "";
    setForm(init);
    setSheetOpen(true);
  };

  const openEdit = (row: T) => {
    setEditingId(row.id);
    setForm(config.toForm(row));
    setSheetOpen(true);
  };

  const save = async () => {
    setSaving(true);
    try {
      const payload = config.fromForm(form);
      if (editingId == null) {
        await config.createFn(payload);
        toast.success(t("dashboard.crud.created", "Created successfully."));
      } else if (config.updateFn) {
        await config.updateFn(editingId, payload);
        toast.success(t("dashboard.crud.updated", "Updated successfully."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.crud.save_error", "Save failed."));
    } finally {
      setSaving(false);
    }
  };

  const remove = async (id: number) => {
    if (!config.deleteFn) return;
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null || !config.deleteFn) return;
    try {
      await config.deleteFn(deletingId);
      toast.success(t("dashboard.crud.deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.crud.delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns = useMemo(() => {
    const base = config.columns(t);
    if (config.updateFn || config.deleteFn) {
      return [
        ...base,
        {
          id: "actions",
          header: "",
          cell: ({ row }: { row: { original: T } }) => (
            <div className="flex justify-end gap-1">
              {config.updateFn && (
                <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => openEdit(row.original)}>
                  <Pencil className="size-3.5" />
                </Button>
              )}
              <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => void remove(row.original.id)}>
                <Trash2 className="size-3.5" />
              </Button>
            </div>
          ),
        },
      ] as Array<ColumnDef<T>>;
    }
    return base;
  }, [t, config, openEdit, remove]);

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
        <h1 className="text-xl font-semibold">{t(config.titleKey, config.titleFallback)}</h1>
        <p className="mt-1 text-sm text-muted-foreground">{t(config.subtitleKey, config.subtitleFallback)}</p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t(config.createLabelKey, config.createLabelFallback)}
          </Button>
        }
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.crud.create", "Create")
                : t("dashboard.crud.edit", "Edit")}
            </SheetTitle>
            <SheetDescription>
              {t("dashboard.crud.sheet_desc", "Fill in the details below.")}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            {config.fields.map((field) => (
              <div key={field.name} className="space-y-2">
                <Label htmlFor={`field-${field.name}`}>{field.label}</Label>
                {field.type === "textarea" ? (
                  <textarea
                    id={`field-${field.name}`}
                    className="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                    value={form[field.name] ?? ""}
                    onChange={(e) => setForm((f) => ({ ...f, [field.name]: e.target.value }))}
                    placeholder={field.placeholder}
                  />
                ) : field.type === "richtext" ? (
                  <RichTextEditor
                    value={form[field.name] ?? ""}
                    onChange={(value) => setForm((f) => ({ ...f, [field.name]: value }))}
                    placeholder={field.placeholder}
                  />
                ) : field.type === "select" ? (
                  <select
                    id={`field-${field.name}`}
                    className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                    value={form[field.name] ?? ""}
                    onChange={(e) => setForm((f) => ({ ...f, [field.name]: e.target.value }))}
                  >
                    {field.options?.map((opt) => (
                      <option key={opt.value} value={opt.value}>
                        {opt.label}
                      </option>
                    ))}
                  </select>
                ) : (
                  <Input
                    id={`field-${field.name}`}
                    type={field.type ?? "text"}
                    value={form[field.name] ?? ""}
                    onChange={(e) => setForm((f) => ({ ...f, [field.name]: e.target.value }))}
                    placeholder={field.placeholder}
                    required={field.required}
                  />
                )}
              </div>
            ))}
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.crud.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.crud.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.crud.confirm_delete", "Delete this item?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
