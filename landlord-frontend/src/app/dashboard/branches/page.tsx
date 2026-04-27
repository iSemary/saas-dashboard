"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2, Upload, Download } from "lucide-react";
import Link from "next/link";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listBranches, createBranch, updateBranch, deleteBranch, fetchBranch, listBrands, type BranchRow } from "@/lib/resources";
import { EntitySelector } from "@/components/entity-selector";
import { useI18n } from "@/context/i18n-context";

export default function BranchesPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<BranchRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({
    name: "", slug: "", description: "", email: "", phone: "", address: "", status: "active", brand_id: "",
  });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const data = await listBranches();
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
    setForm({ name: "", slug: "", description: "", email: "", phone: "", address: "", status: "active", brand_id: "" });
    setSheetOpen(true);
  };

  const openEdit = async (id: number) => {
    setEditingId(id);
    setSheetOpen(true);
    try {
      const branch = await fetchBranch(id);
      setForm({
        name: branch.name,
        slug: branch.slug,
        description: branch.description ?? "",
        email: branch.email ?? "",
        phone: branch.phone ?? "",
        address: branch.address ?? "",
        status: branch.status,
        brand_id: branch.brand_id ? String(branch.brand_id) : "",
      });
    } catch {
      toast.error(t("dashboard.branches.toast_load_error", "Could not load branch."));
      setSheetOpen(false);
    }
  };

  const save = async () => {
    if (!form.name.trim()) {
      toast.error(t("dashboard.branches.name_required", "Name is required."));
      return;
    }
    setSaving(true);
    try {
      const payload = {
        ...form,
        brand_id: form.brand_id ? Number(form.brand_id) : null,
      };
      if (editingId == null) {
        await createBranch(payload);
        toast.success(t("dashboard.branches.toast_created", "Branch created."));
      } else {
        await updateBranch(editingId, payload);
        toast.success(t("dashboard.branches.toast_updated", "Branch updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.branches.toast_save_error", "Save failed."));
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
      await deleteBranch(deletingId);
      toast.success(t("dashboard.branches.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.branches.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<BranchRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "slug", header: t("dashboard.branches.slug", "Slug") },
      {
        accessorKey: "brand",
        header: t("dashboard.branches.brand", "Brand"),
        cell: ({ row }) => row.original.brand?.name ?? "—",
      },
      {
        accessorKey: "status",
        header: t("dashboard.branches.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.status === "active" ? "default" : "secondary"}>
            {row.original.status}
          </Badge>
        ),
      },
      {
        accessorKey: "created_at",
        header: t("dashboard.branches.created_at", "Created"),
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
        <span className="text-sm">{t("dashboard.branches.loading", "Loading…")}</span>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.branches.title", "Branches")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.branches.subtitle", "Manage customer branches.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        enableExport={true}
        toolbarActions={(
          <>
            <Link href="/dashboard/import/branches">
              <Button type="button" variant="outline" size="sm" className="h-9 gap-1">
                <Upload className="size-4" />
                {t("dashboard.import", "Import")}
              </Button>
            </Link>
            <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
              <Plus className="size-4" />
              {t("dashboard.branches.create", "Create Branch")}
            </Button>
          </>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-xl">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.branches.sheet_create_title", "Create Branch")
                : t("dashboard.branches.sheet_edit_title", "Edit Branch")}
            </SheetTitle>
            <SheetDescription>
              {t("dashboard.branches.sheet_desc", "Fill in branch details.")}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="branch-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="branch-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} required />
            </div>
            <div className="space-y-2">
              <Label htmlFor="branch-slug">{t("dashboard.branches.slug", "Slug")}</Label>
              <Input id="branch-slug" value={form.slug} onChange={(e) => setForm((f) => ({ ...f, slug: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="branch-desc">{t("dashboard.branches.description", "Description")}</Label>
              <Input id="branch-desc" value={form.description} onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="branch-email">{t("dashboard.users.col_email", "Email")}</Label>
              <Input id="branch-email" type="email" value={form.email} onChange={(e) => setForm((f) => ({ ...f, email: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="branch-phone">{t("dashboard.branches.phone", "Phone")}</Label>
              <Input id="branch-phone" value={form.phone} onChange={(e) => setForm((f) => ({ ...f, phone: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="branch-address">{t("dashboard.branches.address", "Address")}</Label>
              <Input id="branch-address" value={form.address} onChange={(e) => setForm((f) => ({ ...f, address: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="branch-status">{t("dashboard.branches.status", "Status")}</Label>
              <select
                id="branch-status"
                className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                value={form.status}
                onChange={(e) => setForm((f) => ({ ...f, status: e.target.value }))}
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="branch-brand">{t("dashboard.branches.brand", "Brand")}</Label>
              <EntitySelector
                value={form.brand_id}
                onChange={(v) => setForm((f) => ({ ...f, brand_id: v }))}
                listFn={listBrands}
                optionLabelKey="name"
                optionValueKey="id"
                placeholder={t("dashboard.branches.select_brand", "Select a brand")}
                required
              />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.branches.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.branches.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.branches.confirm_delete", "Delete this branch?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
