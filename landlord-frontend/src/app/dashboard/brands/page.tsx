"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2, Upload } from "lucide-react";
import Link from "next/link";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
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
import { listBrands, createBrand, updateBrand, deleteBrand, fetchBrand, type BrandRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function BrandsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<BrandRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({
    name: "", slug: "", description: "", logo: "", website: "", email: "", phone: "", address: "", status: "active",
  });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const data = await listBrands();
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
    setForm({ name: "", slug: "", description: "", logo: "", website: "", email: "", phone: "", address: "", status: "active" });
    setSheetOpen(true);
  };

  const openEdit = async (id: number) => {
    setEditingId(id);
    setSheetOpen(true);
    try {
      const brand = await fetchBrand(id);
      setForm({
        name: brand.name,
        slug: brand.slug,
        description: brand.description ?? "",
        logo: brand.logo ?? "",
        website: brand.website ?? "",
        email: brand.email ?? "",
        phone: brand.phone ?? "",
        address: brand.address ?? "",
        status: brand.status,
      });
    } catch {
      toast.error(t("dashboard.brands.toast_load_error", "Could not load brand."));
      setSheetOpen(false);
    }
  };

  const save = async () => {
    if (!form.name.trim()) {
      toast.error(t("dashboard.brands.name_required", "Name is required."));
      return;
    }
    setSaving(true);
    try {
      if (editingId == null) {
        await createBrand(form);
        toast.success(t("dashboard.brands.toast_created", "Brand created."));
      } else {
        await updateBrand(editingId, form);
        toast.success(t("dashboard.brands.toast_updated", "Brand updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.brands.toast_save_error", "Save failed."));
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
      await deleteBrand(deletingId);
      toast.success(t("dashboard.brands.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.brands.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<BrandRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "slug", header: t("dashboard.brands.slug", "Slug") },
      {
        accessorKey: "tenant",
        header: t("dashboard.brands.tenant", "Tenant"),
        cell: ({ row }) => row.original.tenant?.name ?? "—",
      },
      {
        accessorKey: "status",
        header: t("dashboard.brands.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.status === "active" ? "default" : "secondary"}>
            {row.original.status}
          </Badge>
        ),
      },
      {
        accessorKey: "created_at",
        header: t("dashboard.brands.created_at", "Created"),
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
        <span className="text-sm">{t("dashboard.brands.loading", "Loading…")}</span>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.brands.title", "Brands")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.brands.subtitle", "Manage customer brands and their details.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        enableRowSelection={true}
        enableExport={true}
        toolbarActions={
          <>
            <Link href="/dashboard/import/brands">
              <Button type="button" variant="outline" size="sm" className="h-9 gap-1">
                <Upload className="size-4" />
                {t("dashboard.import", "Import")}
              </Button>
            </Link>
            <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
              <Plus className="size-4" />
              {t("dashboard.brands.create", "Create Brand")}
            </Button>
          </>
        }
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent 
          className="flex w-full max-w-lg flex-col gap-0 sm:max-w-[50vw] max-h-screen"
          resizable={true}
          defaultWidth={typeof window !== 'undefined' ? window.innerWidth * 0.5 : 600}
          minWidth={320}
          maxWidth={typeof window !== 'undefined' ? window.innerWidth * 0.8 : 1200}
        >
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.brands.sheet_create_title", "Create Brand")
                : t("dashboard.brands.sheet_edit_title", "Edit Brand")}
            </SheetTitle>
            <SheetDescription>
              {t("dashboard.brands.sheet_desc", "Fill in brand details.")}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="brand-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="brand-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} required />
            </div>
            <div className="space-y-2">
              <Label htmlFor="brand-slug">{t("dashboard.brands.slug", "Slug")}</Label>
              <Input id="brand-slug" value={form.slug} onChange={(e) => setForm((f) => ({ ...f, slug: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="brand-desc">{t("dashboard.brands.description", "Description")}</Label>
              <Input id="brand-desc" value={form.description} onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="brand-logo">{t("dashboard.brands.logo", "Logo")}</Label>
              <FileUpload
                onUploadComplete={(files) => {
                  if (files.length > 0) {
                    setForm((f) => ({ ...f, logo: files[0].url }));
                  }
                }}
                accept="image/*"
                maxFiles={1}
                uploadType="media"
              />
              {form.logo && (
                <div className="mt-2">
                  <img src={form.logo} alt="Logo" className="h-16 w-16 object-contain rounded border" />
                </div>
              )}
            </div>
            <div className="space-y-2">
              <Label htmlFor="brand-website">{t("dashboard.brands.website", "Website")}</Label>
              <Input id="brand-website" type="url" value={form.website} onChange={(e) => setForm((f) => ({ ...f, website: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="brand-email">{t("dashboard.users.col_email", "Email")}</Label>
              <Input id="brand-email" type="email" value={form.email} onChange={(e) => setForm((f) => ({ ...f, email: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="brand-phone">{t("dashboard.brands.phone", "Phone")}</Label>
              <Input id="brand-phone" value={form.phone} onChange={(e) => setForm((f) => ({ ...f, phone: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="brand-address">{t("dashboard.brands.address", "Address")}</Label>
              <Input id="brand-address" value={form.address} onChange={(e) => setForm((f) => ({ ...f, address: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="brand-status">{t("dashboard.brands.status", "Status")}</Label>
              <select
                id="brand-status"
                className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                value={form.status}
                onChange={(e) => setForm((f) => ({ ...f, status: e.target.value }))}
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.brands.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.brands.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.brands.confirm_delete", "Delete this brand?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
