"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2, Shield, UserCircle, Phone } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Checkbox } from "@/components/ui/checkbox";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import { PhoneInput } from "@/components/ui/phone-input";
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
import { useAnimation } from "@/context/animation-context";
import { Blur } from "@/components/animate-ui/primitives/effects/blur";
import { listUsers, createUser, updateUser, deleteUser, listRoles, listPermissions } from "@/lib/tenant-resources";
import type { TableParams } from "@/lib/tenant-resources";

type Role = { id: number; name: string };
type Permission = { id: number; name: string };

type User = {
  id: number;
  name: string;
  email: string;
  phone?: string;
  is_active?: boolean;
  created_at?: string;
  roles?: Role[];
  permissions?: Permission[];
  roles_count?: number;
  permissions_count?: number;
};

type UserForm = {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone: string;
  is_active: boolean;
  role_ids: number[];
  permission_ids: number[];
};

function AnimatedSheetContent({ children, enabled }: { children: React.ReactNode; enabled: boolean }) {
  if (!enabled) return <>{children}</>;
  return (
    <Blur inView inViewOnce delay={100}>
      {children}
    </Blur>
  );
}

const initialForm: UserForm = {
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  phone: "",
  is_active: true,
  role_ids: [],
  permission_ids: [],
};

export default function UsersPage() {
  const { t } = useI18n();
  const { enabled: animationEnabled } = useAnimation();
  const [rows, setRows] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [roles, setRoles] = useState<Role[]>([]);
  const [permissions, setPermissions] = useState<Permission[]>([]);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState<UserForm>(initialForm);
  const [errors, setErrors] = useState<Record<string, string>>({});

  const load = useCallback(async (params?: TableParams) => {
    setLoading(true);
    try {
      const result = await listUsers(params);
      if (Array.isArray(result)) {
        setRows(result as User[]);
      } else {
        setRows((result as { data?: User[] })?.data ?? []);
      }
    } catch {
      setRows([]);
    } finally {
      setLoading(false);
    }
  }, []);

  const loadRoles = useCallback(async () => {
    try {
      const result = await listRoles();
      setRoles((result as Role[]) ?? []);
    } catch {
      setRoles([]);
    }
  }, []);

  const loadPermissions = useCallback(async () => {
    try {
      const result = await listPermissions();
      setPermissions((result as Permission[]) ?? []);
    } catch {
      setPermissions([]);
    }
  }, []);

  useEffect(() => {
    void load();
    void loadRoles();
    void loadPermissions();
  }, [load, loadRoles, loadPermissions]);

  const openCreate = () => {
    setEditingId(null);
    setForm(initialForm);
    setErrors({});
    setSheetOpen(true);
  };

  const openEdit = (user: User) => {
    setEditingId(user.id);
    setForm({
      name: user.name,
      email: user.email,
      password: "",
      password_confirmation: "",
      phone: user.phone ?? "",
      is_active: user.is_active ?? true,
      role_ids: user.roles?.map((r) => r.id) ?? [],
      permission_ids: user.permissions?.map((p) => p.id) ?? [],
    });
    setErrors({});
    setSheetOpen(true);
  };

  const validateForm = (): boolean => {
    const newErrors: Record<string, string> = {};

    if (!form.name.trim()) {
      newErrors.name = t("validation.required", "This field is required");
    }

    if (!form.email.trim()) {
      newErrors.email = t("validation.required", "This field is required");
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
      newErrors.email = t("validation.email", "Invalid email address");
    }

    if (editingId === null) {
      if (!form.password) {
        newErrors.password = t("validation.required", "This field is required");
      } else if (form.password.length < 8) {
        newErrors.password = t("validation.min_length", "Password must be at least 8 characters");
      }
    }

    if (form.password && form.password !== form.password_confirmation) {
      newErrors.password_confirmation = t("validation.password_match", "Passwords do not match");
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  const save = async () => {
    if (!validateForm()) return;

    setSaving(true);
    try {
      const payload: Record<string, unknown> = {
        name: form.name,
        email: form.email,
        phone: form.phone || null,
        is_active: form.is_active,
        role_ids: form.role_ids,
        permission_ids: form.permission_ids,
      };

      if (form.password) {
        payload.password = form.password;
        payload.password_confirmation = form.password_confirmation;
      }

      if (editingId === null) {
        await createUser(payload);
        toast.success(t("dashboard.crud.created", "Created successfully."));
      } else {
        await updateUser(editingId, payload);
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

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await deleteUser(deletingId);
      toast.success(t("dashboard.crud.deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.crud.delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
      setConfirmOpen(false);
    }
  };

  const toggleRole = (roleId: number) => {
    setForm((f) => ({
      ...f,
      role_ids: f.role_ids.includes(roleId)
        ? f.role_ids.filter((id) => id !== roleId)
        : [...f.role_ids, roleId],
    }));
  };

  const togglePermission = (permissionId: number) => {
    setForm((f) => ({
      ...f,
      permission_ids: f.permission_ids.includes(permissionId)
        ? f.permission_ids.filter((id) => id !== permissionId)
        : [...f.permission_ids, permissionId],
    }));
  };

  const columns = useMemo((): Array<ColumnDef<User>> => {
    return [
      { accessorKey: "id", header: t("dashboard.table.id", "ID") },
      {
        accessorKey: "name",
        header: t("dashboard.table.name", "Name"),
        cell: ({ row }) => (
          <div className="flex items-center gap-2">
            <UserCircle className="size-4 text-muted-foreground" />
            <span>{row.original.name}</span>
          </div>
        ),
      },
      { accessorKey: "email", header: t("dashboard.table.email", "Email") },
      {
        accessorKey: "phone",
        header: t("dashboard.table.phone", "Phone"),
        cell: ({ row }) => row.original.phone || "—",
      },
      {
        accessorKey: "roles_count",
        header: t("dashboard.table.roles", "Roles"),
        cell: ({ row }) => (
          <div className="flex items-center gap-1">
            <Shield className="size-3.5 text-muted-foreground" />
            <span>{row.original.roles_count ?? row.original.roles?.length ?? 0}</span>
          </div>
        ),
      },
      {
        accessorKey: "is_active",
        header: t("dashboard.table.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.is_active ? "default" : "secondary"}>
            {row.original.is_active
              ? t("dashboard.status.active", "Active")
              : t("dashboard.status.inactive", "Inactive")}
          </Badge>
        ),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button
              type="button"
              variant="outline"
              size="sm"
              className="h-8"
              onClick={() => openEdit(row.original)}
            >
              <Pencil className="size-3.5" />
            </Button>
            <Button
              type="button"
              variant="outline"
              size="sm"
              className="h-8 text-destructive"
              onClick={() => {
                setDeletingId(row.original.id);
                setConfirmOpen(true);
              }}
            >
              <Trash2 className="size-3.5" />
            </Button>
          </div>
        ),
      },
    ];
  }, [t]);

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
          <h1 className="text-xl font-semibold">
            {t("dashboard.users.title", "Users")}
          </h1>
          <p className="mt-1 text-sm text-muted-foreground">
            {t("dashboard.users.subtitle", "Manage tenant users")}
          </p>
        </div>
        <Button type="button" size="sm" className="h-9 gap-1 shrink-0" onClick={openCreate}>
          <Plus className="size-4" />
          {t("dashboard.users.create", "New User")}
        </Button>
      </div>

      <DataTable
        columns={columns}
        data={rows}
        enableExport={true}
        searchable={true}
      />

      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-xl flex-col gap-0 sm:max-w-lg overflow-y-auto">
          <AnimatedSheetContent enabled={animationEnabled}>
            <SheetHeader className="pb-4">
              <SheetTitle>
                {editingId === null
                  ? t("dashboard.crud.create", "Create User")
                  : t("dashboard.crud.edit", "Edit User")}
              </SheetTitle>
              <SheetDescription>
                {t("dashboard.users.form_desc", "Fill in the user details below.")}
              </SheetDescription>
            </SheetHeader>

            <div className="flex-1 space-y-6 px-4 py-4">
              <div className="space-y-4">
                <h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wide">
                  {t("dashboard.users.basic_info", "Basic Information")}
                </h3>

                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="name">
                      {t("dashboard.users.name", "Name")}
                      <span className="text-destructive ml-1">*</span>
                    </Label>
                    <Input
                      id="name"
                      value={form.name}
                      onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))}
                      placeholder={t("dashboard.users.name_placeholder", "John Doe")}
                      className={errors.name ? "border-destructive" : ""}
                    />
                    {errors.name && (
                      <p className="text-xs text-destructive">{errors.name}</p>
                    )}
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="email">
                      {t("dashboard.users.email", "Email")}
                      <span className="text-destructive ml-1">*</span>
                    </Label>
                    <Input
                      id="email"
                      type="email"
                      value={form.email}
                      onChange={(e) => setForm((f) => ({ ...f, email: e.target.value }))}
                      placeholder={t("dashboard.users.email_placeholder", "john@example.com")}
                      className={errors.email ? "border-destructive" : ""}
                    />
                    {errors.email && (
                      <p className="text-xs text-destructive">{errors.email}</p>
                    )}
                  </div>
                </div>

                <div className="space-y-2">
                  <Label htmlFor="phone">
                    <Phone className="inline size-4 mr-1" />
                    {t("dashboard.users.phone", "Phone")}
                  </Label>
                  <PhoneInput
                    id="phone"
                    value={form.phone}
                    onChange={(value) => setForm((f) => ({ ...f, phone: value ?? "" }))}
                    placeholder={t("dashboard.users.phone_placeholder", "+1 234 567 8900")}
                  />
                </div>

                <label className="flex items-center justify-between rounded-lg border p-3 cursor-pointer hover:bg-muted/50 transition-colors">
                  <div className="space-y-0.5">
                    <span className="text-sm font-medium">
                      {t("dashboard.users.is_active", "Active Account")}
                    </span>
                    <p className="text-xs text-muted-foreground">
                      {t("dashboard.users.is_active_desc", "User can log in and access the system")}
                    </p>
                  </div>
                  <Checkbox
                    checked={form.is_active}
                    onCheckedChange={(checked: boolean) => setForm((f) => ({ ...f, is_active: checked }))}
                  />
                </label>
              </div>

              <Separator />

              <div className="space-y-4">
                <h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wide">
                  {t("dashboard.users.password_section", "Password")}
                </h3>

                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="password">
                      {t("dashboard.users.password", "Password")}
                      {editingId === null && <span className="text-destructive ml-1">*</span>}
                    </Label>
                    <Input
                      id="password"
                      type="password"
                      value={form.password}
                      onChange={(e) => setForm((f) => ({ ...f, password: e.target.value }))}
                      placeholder={editingId === null ? "••••••••" : t("dashboard.users.password_leave_empty", "Leave empty to keep current")}
                      className={errors.password ? "border-destructive" : ""}
                    />
                    {errors.password && (
                      <p className="text-xs text-destructive">{errors.password}</p>
                    )}
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="password_confirmation">
                      {t("dashboard.users.password_confirmation", "Confirm Password")}
                    </Label>
                    <Input
                      id="password_confirmation"
                      type="password"
                      value={form.password_confirmation}
                      onChange={(e) => setForm((f) => ({ ...f, password_confirmation: e.target.value }))}
                      placeholder={t("dashboard.users.confirm_password_placeholder", "••••••••")}
                      className={errors.password_confirmation ? "border-destructive" : ""}
                    />
                    {errors.password_confirmation && (
                      <p className="text-xs text-destructive">{errors.password_confirmation}</p>
                    )}
                  </div>
                </div>
              </div>

              <Separator />

              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wide">
                    <Shield className="inline size-4 mr-1" />
                    {t("dashboard.users.roles", "Roles")}
                  </h3>
                  <span className="text-xs text-muted-foreground">
                    {form.role_ids.length} {t("dashboard.users.selected", "selected")}
                  </span>
                </div>

                <div className="grid gap-2 sm:grid-cols-2">
                  {roles.map((role) => (
                    <label
                      key={role.id}
                      className="flex items-center gap-3 rounded-md border p-3 cursor-pointer hover:bg-muted/50 transition-colors"
                    >
                      <Checkbox
                        checked={form.role_ids.includes(role.id)}
                        onCheckedChange={() => toggleRole(role.id)}
                      />
                      <span className="text-sm font-medium">{role.name}</span>
                    </label>
                  ))}
                </div>

                {roles.length === 0 && (
                  <p className="text-sm text-muted-foreground text-center py-4">
                    {t("dashboard.users.no_roles", "No roles available")}
                  </p>
                )}
              </div>

              <Separator />

              <div className="space-y-4">
                <div className="flex items-center justify-between">
                  <h3 className="text-sm font-medium text-muted-foreground uppercase tracking-wide">
                    {t("dashboard.users.permissions", "Direct Permissions")}
                  </h3>
                  <span className="text-xs text-muted-foreground">
                    {form.permission_ids.length} {t("dashboard.users.selected", "selected")}
                  </span>
                </div>

                <div className="grid gap-2 sm:grid-cols-2 max-h-60 overflow-y-auto">
                  {permissions.map((permission) => (
                    <label
                      key={permission.id}
                      className="flex items-center gap-3 rounded-md border p-2 cursor-pointer hover:bg-muted/50 transition-colors"
                    >
                      <Checkbox
                        checked={form.permission_ids.includes(permission.id)}
                        onCheckedChange={() => togglePermission(permission.id)}
                      />
                      <span className="text-sm">{permission.name}</span>
                    </label>
                  ))}
                </div>

                {permissions.length === 0 && (
                  <p className="text-sm text-muted-foreground text-center py-4">
                    {t("dashboard.users.no_permissions", "No permissions available")}
                  </p>
                )}
              </div>
            </div>

            <SheetFooter className="border-t border-border/80 px-4 py-3 mt-auto">
              <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
                {t("dashboard.actions.cancel", "Cancel")}
              </Button>
              <Button type="button" disabled={saving} onClick={() => void save()}>
                {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.crud.save", "Save")}
              </Button>
            </SheetFooter>
          </AnimatedSheetContent>
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
