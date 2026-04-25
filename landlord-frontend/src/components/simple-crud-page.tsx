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
import { RichTextEditor } from "@/components/ui/rich-text-editor";
import { FileUpload } from "@/components/ui/file-upload";
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
import type { TableParams, PaginatedResponse } from "@/lib/resources";
import { EntitySelector } from "@/components/entity-selector";
import { NavigationItemsEditor } from "@/components/navigation-items-editor";
import Link from "next/link";

export type FieldDef = {
  name: string;
  label: string;
  type?: "text" | "email" | "password" | "number" | "url" | "textarea" | "select" | "richtext" | "slug" | "entity" | "file" | "datetime" | "navItems";
  placeholder?: string;
  required?: boolean;
  options?: Array<{ value: string; label: string }>;
  sourceField?: string;
  /** For entity type: function to fetch options */
  listFn?: () => Promise<unknown[]>;
  /** For entity type: property key for option label (default: "name") */
  optionLabelKey?: string;
  /** For entity type: property key for option value (default: "id") */
  optionValueKey?: string;
  /** For entity type: property key for parent entity (for hierarchical display) */
  parentKey?: string;
  /** For entity type: property key for parent label (default: "name") */
  parentLabelKey?: string;
  /** For file type: accepted file types (e.g., "image/*") */
  accept?: string;
};

export type ActionButton = {
  labelKey: string;
  labelFallback: string;
  icon?: React.ComponentType<{ className?: string }>;
  variant?: "default" | "outline" | "destructive" | "secondary" | "ghost" | "link";
  onClick?: () => void | Promise<void>;
  href?: string;
  className?: string;
  disabled?: boolean;
  iconClassName?: string;
};

export type SimpleCRUDConfig<T extends { id: number }> = {
  titleKey: string;
  titleFallback: string;
  subtitleKey: string;
  subtitleFallback: string;
  createLabelKey: string;
  createLabelFallback: string;
  fields: FieldDef[];
  /** List function - supports both client-side and server-side */
  listFn: (params?: TableParams) => Promise<T[]> | Promise<PaginatedResponse<T>>;
  createFn: (payload: Record<string, unknown>) => Promise<unknown>;
  updateFn?: ((id: number, payload: Record<string, unknown>) => Promise<unknown>) | null;
  deleteFn?: ((id: number) => Promise<void>) | null;
  columns: (t: (key: string, fallback: string) => string) => Array<ColumnDef<T>>;
  toForm: (row: T) => Record<string, string>;
  fromForm: (form: Record<string, string>) => Record<string, unknown>;
  /** Enable server-side table operations (search, sort, pagination) */
  serverSide?: boolean;
  /** Searchable columns for backend search (required when serverSide=true) */
  searchableColumns?: string[];
  /** Sortable columns for backend sort (required when serverSide=true) */
  sortableColumns?: string[];
  /** Custom action buttons to display in the header */
  actionButtons?: ActionButton[];
};


export function SimpleCRUDPage<T extends { id: number }>({
  config,
}: {
  config: SimpleCRUDConfig<T>;
}) {
  const { t } = useI18n();
  const [rows, setRows] = useState<T[]>([]);
  const [loading, setLoading] = useState(true);
  const [tableMeta, setTableMeta] = useState<{ current_page: number; last_page: number; per_page: number; total: number } | undefined>(undefined);
  const serverSide = config.serverSide ?? false;
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

  const load = useCallback(async (params?: TableParams) => {
    setLoading(true);
    try {
      const result = await config.listFn(params);
      // Handle both array and paginated response
      if (Array.isArray(result)) {
        setRows(result);
        setTableMeta(undefined);
      } else {
        setRows(result.data ?? []);
        setTableMeta(result.meta);
      }
    } catch {
      setRows([]);
      setTableMeta(undefined);
    } finally {
      setLoading(false);
    }
  }, [config]);

  useEffect(() => {
    // Initial load - only for client-side mode
    if (!serverSide) {
      void load();
    } else {
      // Server-side: load with default params
      void load({ page: 1, per_page: 10 });
    }
  }, [load, serverSide]);

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
      await load(serverSide ? { page: 1, per_page: tableMeta?.per_page ?? 10 } : undefined);
    } catch {
      toast.error(t("dashboard.crud.delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const handleTableChange = useCallback((params: { page: number; perPage: number; search: string; sortBy: string | null; sortDirection: 'asc' | 'desc' }) => {
    if (!serverSide) return;
    
    const tableParams: TableParams = {
      page: params.page,
      per_page: params.perPage,
      search: params.search || undefined,
      sort_by: params.sortBy || undefined,
      sort_direction: params.sortDirection,
    };
    
    void load(tableParams);
  }, [load, serverSide]);

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
      <div className="rounded-xl border bg-muted/40 p-4 flex items-center justify-between gap-4">
        <div>
          <h1 className="text-xl font-semibold">{t(config.titleKey, config.titleFallback)}</h1>
          <p className="mt-1 text-sm text-muted-foreground">{t(config.subtitleKey, config.subtitleFallback)}</p>
        </div>
        <div className="flex items-center gap-2">
          {config.actionButtons?.map((btn, idx) => {
            const Icon = btn.icon;
            if (btn.href) {
              return (
                <Link key={idx} href={btn.href}>
                  <Button
                    type="button"
                    size="sm"
                    variant={btn.variant ?? "outline"}
                    className={`h-9 gap-1 shrink-0 ${btn.className ?? ""}`}
                    disabled={btn.disabled}
                  >
                    {Icon && <Icon className={`size-4 ${btn.iconClassName ?? ""}`} />}
                    {t(btn.labelKey, btn.labelFallback)}
                  </Button>
                </Link>
              );
            }
            return (
              <Button
                key={idx}
                type="button"
                size="sm"
                variant={btn.variant ?? "outline"}
                className={`h-9 gap-1 shrink-0 ${btn.className ?? ""}`}
                onClick={() => void btn.onClick?.()}
                disabled={btn.disabled}
              >
                {Icon && <Icon className={`size-4 ${btn.iconClassName ?? ""}`} />}
                {t(btn.labelKey, btn.labelFallback)}
              </Button>
            );
          })}
          <Button
            type="button"
            size="sm"
            className="h-9 gap-1 shrink-0"
            onClick={openCreate}
          >
            <Plus className="size-4" />
            {t(config.createLabelKey, config.createLabelFallback)}
          </Button>
        </div>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        enableExport={true}
        searchable={true}
        serverSide={serverSide}
        meta={tableMeta}
        loading={loading}
        onTableChange={serverSide ? handleTableChange : undefined}
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
                ) : field.type === "slug" ? (
                  <SlugInput
                    id={`field-${field.name}`}
                    value={form[field.name] ?? ""}
                    onChange={(v) => setForm((f) => ({ ...f, [field.name]: v }))}
                    sourceValue={field.sourceField ? form[field.sourceField] : undefined}
                    placeholder={field.placeholder}
                    required={field.required}
                  />
                ) : field.type === "entity" && field.listFn ? (
                  <EntitySelector
                    value={form[field.name] ?? ""}
                    onChange={(v) => setForm((f) => ({ ...f, [field.name]: v }))}
                    listFn={field.listFn}
                    optionLabelKey={field.optionLabelKey}
                    optionValueKey={field.optionValueKey}
                    parentKey={field.parentKey}
                    parentLabelKey={field.parentLabelKey}
                    placeholder={field.placeholder}
                    required={field.required}
                    disabled={saving}
                  />
                ) : field.type === "datetime" ? (
                  <Input
                    id={`field-${field.name}`}
                    type="datetime-local"
                    value={form[field.name] ?? ""}
                    onChange={(e) => setForm((f) => ({ ...f, [field.name]: e.target.value }))}
                    placeholder={field.placeholder}
                    required={field.required}
                  />
                ) : field.type === "navItems" ? (
                  <NavigationItemsEditor
                    value={form[field.name] ?? ""}
                    onChange={(v) => setForm((f) => ({ ...f, [field.name]: v }))}
                    disabled={saving}
                  />
                ) : field.type === "file" ? (
                  <div className="space-y-2">
                    <FileUpload
                      accept={field.accept ?? "image/*"}
                      maxFiles={1}
                      onFileSelect={(files) => {
                        if (files.length > 0) {
                          setForm((f) => ({ ...f, [field.name]: files[0].name }));
                        }
                      }}
                      disabled={saving}
                    />
                    {form[field.name] && (
                      <p className="text-xs text-muted-foreground">Selected: {form[field.name]}</p>
                    )}
                  </div>
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
