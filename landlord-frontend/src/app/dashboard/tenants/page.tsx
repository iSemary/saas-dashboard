"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Upload, Pencil, Trash2 } from "lucide-react";
import Link from "next/link";
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
import { listTenants, createTenant, updateTenant, deleteTenant, fetchTenant, type TenantRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function TenantsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<TenantRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ name: "", domain: "", database: "" });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const data = await listTenants();
      setRows(data);
    } catch {
      setRows([]);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void load();
  }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setForm({ name: "", domain: "", database: "" });
    setSheetOpen(true);
  };

  const openEdit = async (id: number) => {
    setEditingId(id);
    setSheetOpen(true);
    try {
      const tenant = await fetchTenant(id);
      setForm({ name: tenant.name, domain: tenant.domain, database: tenant.database });
    } catch {
      toast.error(t("dashboard.tenants.toast_load_error", "Could not load tenant."));
      setSheetOpen(false);
    }
  };

  const save = async () => {
    if (!form.name.trim() || !form.domain.trim() || !form.database.trim()) {
      toast.error(t("dashboard.tenants.required_fields", "Name, domain, and database are required."));
      return;
    }
    setSaving(true);
    try {
      if (editingId == null) {
        await createTenant(form);
        toast.success(t("dashboard.tenants.toast_created", "Tenant created."));
      } else {
        await updateTenant(editingId, form);
        toast.success(t("dashboard.tenants.toast_updated", "Tenant updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.tenants.toast_save_error", "Save failed."));
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
      await deleteTenant(deletingId);
      toast.success(t("dashboard.tenants.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.tenants.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<TenantRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "domain", header: t("dashboard.tenants.domain", "Domain") },
      { accessorKey: "database", header: t("dashboard.tenants.database", "Database") },
      {
        accessorKey: "created_at",
        header: t("dashboard.tenants.created_at", "Created"),
        cell: ({ row }) => row.original.created_at ? new Date(row.original.created_at).toLocaleDateString() : "—",
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
        <span className="text-sm">{t("dashboard.tenants.loading", "Loading…")}</span>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.tenants.title", "Tenants")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.tenants.subtitle", "Manage tenant databases and domains.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        enableExport={true}
        toolbarActions={(
          <>
            <Link href="/dashboard/import/tenants">
              <Button type="button" variant="outline" size="sm" className="h-9 gap-1">
                <Upload className="size-4" />
                {t("dashboard.import", "Import")}
              </Button>
            </Link>
            <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
              <Plus className="size-4" />
              {t("dashboard.tenants.create", "Create Tenant")}
            </Button>
          </>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.tenants.sheet_create_title", "Create Tenant")
                : t("dashboard.tenants.sheet_edit_title", "Edit Tenant")}
            </SheetTitle>
            <SheetDescription>
              {t("dashboard.tenants.sheet_desc", "Set up tenant name, domain, and database.")}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="tenant-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="tenant-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} required />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tenant-domain">{t("dashboard.tenants.domain", "Domain")}</Label>
              <Input id="tenant-domain" value={form.domain} onChange={(e) => setForm((f) => ({ ...f, domain: e.target.value }))} required />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tenant-database">{t("dashboard.tenants.database", "Database")}</Label>
              <Input id="tenant-database" value={form.database} onChange={(e) => setForm((f) => ({ ...f, database: e.target.value }))} required />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.tenants.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.tenants.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.tenants.confirm_delete", "Delete this tenant?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
