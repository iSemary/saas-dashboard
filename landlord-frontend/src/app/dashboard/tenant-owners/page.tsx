"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2, ShieldCheck } from "lucide-react";
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
import {
  listTenantOwners,
  createTenantOwner,
  updateTenantOwner,
  deleteTenantOwner,
  fetchTenantOwner,
  listTenants,
  listUsers,
  type TenantOwnerRow,
  type TenantRow,
} from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function TenantOwnersPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<TenantOwnerRow[]>([]);
  const [tenants, setTenants] = useState<TenantRow[]>([]);
  const [users, setUsers] = useState<Array<{ id: number; name: string; email: string }>>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [filterTenantId, setFilterTenantId] = useState<string>("");
  const [filterStatus, setFilterStatus] = useState<string>("");
  const [form, setForm] = useState({
    tenant_id: "",
    user_id: "",
    role: "owner",
    is_super_admin: false,
    status: "active",
  });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const params: Parameters<typeof listTenantOwners>[0] = {};
      if (filterTenantId) params.tenant_id = Number(filterTenantId);
      if (filterStatus) params.status = filterStatus;
      const data = await listTenantOwners(params);
      setRows(data);
    } catch {
      setRows([]);
    } finally {
      setLoading(false);
    }
  }, [filterTenantId, filterStatus]);

  useEffect(() => {
    void load();
  }, [load]);

  useEffect(() => {
    void listTenants().then(setTenants).catch(() => setTenants([]));
    void listUsers().then((data) => setUsers(data as Array<{ id: number; name: string; email: string }>)).catch(() => setUsers([]));
  }, []);

  const openCreate = () => {
    setEditingId(null);
    setForm({ tenant_id: "", user_id: "", role: "owner", is_super_admin: false, status: "active" });
    setSheetOpen(true);
  };

  const openEdit = async (id: number) => {
    setEditingId(id);
    setSheetOpen(true);
    try {
      const owner = await fetchTenantOwner(id);
      setForm({
        tenant_id: owner.tenant_id ? String(owner.tenant_id) : "",
        user_id: owner.user_id ? String(owner.user_id) : "",
        role: owner.role ?? "owner",
        is_super_admin: owner.is_super_admin ?? false,
        status: owner.status ?? "active",
      });
    } catch {
      toast.error(t("dashboard.tenant_owners.toast_load_error", "Could not load tenant owner."));
      setSheetOpen(false);
    }
  };

  const save = async () => {
    if (!form.tenant_id || !form.user_id) {
      toast.error(t("dashboard.tenant_owners.required_fields", "Tenant and User are required."));
      return;
    }
    setSaving(true);
    try {
      const payload = {
        tenant_id: Number(form.tenant_id),
        user_id: Number(form.user_id),
        role: form.role || "owner",
        is_super_admin: form.is_super_admin,
        status: form.status,
      };
      if (editingId == null) {
        await createTenantOwner(payload);
        toast.success(t("dashboard.tenant_owners.toast_created", "Tenant owner created."));
      } else {
        await updateTenantOwner(editingId, payload);
        toast.success(t("dashboard.tenant_owners.toast_updated", "Tenant owner updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.tenant_owners.toast_save_error", "Save failed."));
    } finally {
      setSaving(false);
    }
  };

  const remove = (id: number) => {
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await deleteTenantOwner(deletingId);
      toast.success(t("dashboard.tenant_owners.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.tenant_owners.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const statusVariant = (status: string) => {
    if (status === "active") return "default" as const;
    if (status === "suspended") return "destructive" as const;
    return "secondary" as const;
  };

  const columns: Array<ColumnDef<TenantOwnerRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      {
        id: "user",
        header: t("dashboard.tenant_owners.col_user", "User"),
        cell: ({ row }) => {
          const u = row.original.user;
          return u ? (
            <div>
              <p className="font-medium text-sm">{u.name}</p>
              <p className="text-xs text-muted-foreground">{u.email}</p>
            </div>
          ) : "—";
        },
      },
      {
        id: "tenant",
        header: t("dashboard.brands.tenant", "Tenant"),
        cell: ({ row }) => row.original.tenant?.name ?? "—",
      },
      {
        accessorKey: "role",
        header: t("dashboard.tenant_owners.col_role", "Role"),
        cell: ({ row }) => <span className="capitalize">{row.original.role}</span>,
      },
      {
        accessorKey: "is_super_admin",
        header: t("dashboard.tenant_owners.col_super_admin", "Super Admin"),
        cell: ({ row }) =>
          row.original.is_super_admin ? (
            <Badge variant="default" className="gap-1">
              <ShieldCheck className="size-3" />
              Yes
            </Badge>
          ) : (
            <span className="text-muted-foreground text-sm">No</span>
          ),
      },
      {
        accessorKey: "status",
        header: t("dashboard.brands.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={statusVariant(row.original.status)}>
            {row.original.status}
          </Badge>
        ),
      },
      {
        accessorKey: "created_at",
        header: t("dashboard.branches.created_at", "Created"),
        cell: ({ row }) =>
          row.original.created_at ? new Date(row.original.created_at).toLocaleDateString() : "—",
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => void openEdit(row.original.id)}>
              <Pencil className="size-3.5" />
            </Button>
            <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => remove(row.original.id)}>
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
        <span className="text-sm">{t("dashboard.tenant_owners.loading", "Loading…")}</span>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.tenant_owners.title", "Tenant Owners")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.tenant_owners.subtitle", "Manage user assignments and roles per tenant.")}
        </p>
      </div>

      {/* Filters */}
      <div className="flex flex-wrap gap-3">
        <div className="w-48">
          <select
            className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
            value={filterTenantId}
            onChange={(e) => setFilterTenantId(e.target.value)}
          >
            <option value="">{t("dashboard.tenant_owners.all_tenants", "All Tenants")}</option>
            {tenants.map((tn) => (
              <option key={tn.id} value={String(tn.id)}>{tn.name}</option>
            ))}
          </select>
        </div>
        <div className="w-40">
          <select
            className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
          >
            <option value="">{t("dashboard.tenant_owners.all_statuses", "All Statuses")}</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="suspended">Suspended</option>
          </select>
        </div>
      </div>

      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.tenant_owners.create", "Add Tenant Owner")}
          </Button>
        )}
      />

      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.tenant_owners.sheet_create_title", "Add Tenant Owner")
                : t("dashboard.tenant_owners.sheet_edit_title", "Edit Tenant Owner")}
            </SheetTitle>
            <SheetDescription>
              {t("dashboard.tenant_owners.sheet_desc", "Assign a user to a tenant with a role.")}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="owner-tenant">{t("dashboard.brands.tenant", "Tenant")} *</Label>
              <select
                id="owner-tenant"
                className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                value={form.tenant_id}
                onChange={(e) => setForm((f) => ({ ...f, tenant_id: e.target.value }))}
                required
              >
                <option value="">{t("dashboard.tenant_owners.select_tenant", "Select a tenant…")}</option>
                {tenants.map((tn) => (
                  <option key={tn.id} value={String(tn.id)}>{tn.name}</option>
                ))}
              </select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="owner-user">{t("dashboard.tenant_owners.col_user", "User")} *</Label>
              <select
                id="owner-user"
                className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                value={form.user_id}
                onChange={(e) => setForm((f) => ({ ...f, user_id: e.target.value }))}
                required
              >
                <option value="">{t("dashboard.tenant_owners.select_user", "Select a user…")}</option>
                {users.map((u) => (
                  <option key={u.id} value={String(u.id)}>{u.name} ({u.email})</option>
                ))}
              </select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="owner-role">{t("dashboard.tenant_owners.col_role", "Role")}</Label>
              <Input
                id="owner-role"
                value={form.role}
                onChange={(e) => setForm((f) => ({ ...f, role: e.target.value }))}
                placeholder="owner"
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="owner-status">{t("dashboard.brands.status", "Status")}</Label>
              <select
                id="owner-status"
                className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                value={form.status}
                onChange={(e) => setForm((f) => ({ ...f, status: e.target.value }))}
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="suspended">Suspended</option>
              </select>
            </div>
            <div className="flex items-center gap-3">
              <input
                id="owner-super-admin"
                type="checkbox"
                className="h-4 w-4 rounded border border-input"
                checked={form.is_super_admin}
                onChange={(e) => setForm((f) => ({ ...f, is_super_admin: e.target.checked }))}
              />
              <Label htmlFor="owner-super-admin" className="cursor-pointer">
                {t("dashboard.tenant_owners.col_super_admin", "Super Admin")}
              </Label>
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.tenant_owners.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>

      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.tenant_owners.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.tenant_owners.confirm_delete", "Remove this tenant owner assignment?")}
        onConfirm={() => void handleConfirmDelete()}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
