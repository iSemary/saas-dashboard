"use client";

import { useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, UsersRound, Plus, Pencil, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listPermissionGroups, syncUserPermissionGroups, type PermissionGroup } from "@/lib/permission-groups";
import { listUsers } from "@/lib/resources";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";

type UserRow = {
  id: number;
  name: string;
  email: string;
  permission_groups?: Array<{ id: number; name: string; slug: string }>;
};

export default function SystemUsersPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<UserRow[]>([]);
  const [groupsSheetOpen, setGroupsSheetOpen] = useState(false);
  const [groupsSheetUser, setGroupsSheetUser] = useState<UserRow | null>(null);
  const [allGroups, setAllGroups] = useState<PermissionGroup[]>([]);
  const [selectedGroupIds, setSelectedGroupIds] = useState<Set<number>>(new Set());
  const [savingGroups, setSavingGroups] = useState(false);
  const [createSheetOpen, setCreateSheetOpen] = useState(false);
  const [createForm, setCreateForm] = useState({ name: "", email: "", password: "" });
  const [creating, setCreating] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);

  const load = async () => {
    const data = await listUsers();
    setRows(data as UserRow[]);
  };

  useEffect(() => {
    listUsers().then((data) => setRows(data as UserRow[])).catch(() => setRows([]));
  }, []);

  const openGroups = async (user: UserRow) => {
    setGroupsSheetUser(user);
    setGroupsSheetOpen(true);
    try {
      const g = await listPermissionGroups();
      setAllGroups(g);
      setSelectedGroupIds(new Set((user.permission_groups ?? []).map((x) => x.id)));
    } catch {
      toast.error(t("dashboard.users.toast_groups_load_error", "Could not load permission groups."));
      setAllGroups([]);
    }
  };

  const saveGroups = async () => {
    if (!groupsSheetUser) return;
    setSavingGroups(true);
    try {
      await syncUserPermissionGroups(groupsSheetUser.id, [...selectedGroupIds]);
      toast.success(t("dashboard.users.toast_groups_ok", "Permission groups updated."));
      setGroupsSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.users.toast_groups_error", "Could not save."));
    } finally {
      setSavingGroups(false);
    }
  };

  const handleCreate = async () => {
    setCreating(true);
    try {
      await api.post("/users", createForm);
      toast.success(t("dashboard.users.toast_created", "User created."));
      setCreateSheetOpen(false);
      setCreateForm({ name: "", email: "", password: "" });
      await load();
    } catch {
      toast.error(t("dashboard.users.toast_create_error", "Could not create user."));
    } finally {
      setCreating(false);
    }
  };

  const handleDelete = async (id: number) => {
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await api.delete(`/users/${deletingId}`);
      toast.success(t("dashboard.users.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.users.toast_delete_error", "Could not delete."));
    }
  };

  const columns: Array<ColumnDef<UserRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "email", header: t("dashboard.users.col_email", "Email") },
      {
        id: "groups",
        header: t("dashboard.users.groups_col", "Groups"),
        cell: ({ row }) => (
          <span className="text-muted-foreground text-xs">
            {(row.original.permission_groups ?? []).map((g) => g.name).join(", ") || "—"}
          </span>
        ),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8 gap-1" onClick={() => void openGroups(row.original)}>
              <UsersRound className="size-3.5" />
              {t("dashboard.users.groups_button", "Groups")}
            </Button>
            <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => void handleDelete(row.original.id)}>
              <Trash2 className="size-3.5" />
            </Button>
          </div>
        ),
      },
    ],
    [t],
  );

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.users.title", "System Users")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.users.subtitle", "Manage dashboard users, emails, roles, and permission groups.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={() => setCreateSheetOpen(true)}>
            <Plus className="size-4" />
            {t("dashboard.users.create", "Create User")}
          </Button>
        )}
      />

      {/* Permission Groups Sheet */}
      <Sheet open={groupsSheetOpen} onOpenChange={setGroupsSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>{t("dashboard.users.sheet_title", "Permission groups")}</SheetTitle>
            <SheetDescription>
              {groupsSheetUser
                ? `${t("dashboard.users.sheet_assign", "Assign groups for")} ${groupsSheetUser.name} (${groupsSheetUser.email})`
                : ""}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto px-4 pb-4">
            <div className="max-h-64 space-y-2 overflow-y-auto rounded-md border border-border/80 p-3">
              {allGroups.length === 0 ? (
                <p className="text-sm text-muted-foreground">
                  {t("dashboard.users.no_groups", "No groups defined. Create some under Permission groups.")}
                </p>
              ) : (
                allGroups.map((g) => (
                  <label key={g.id} className="flex cursor-pointer items-center gap-2 text-sm">
                    <input
                      type="checkbox"
                      className="rounded border-input"
                      checked={selectedGroupIds.has(g.id)}
                      onChange={(e) => {
                        setSelectedGroupIds((prev) => {
                          const next = new Set(prev);
                          if (e.target.checked) next.add(g.id);
                          else next.delete(g.id);
                          return next;
                        });
                      }}
                    />
                    <span>{g.name}</span>
                    <span className="font-mono text-xs text-muted-foreground">{g.slug}</span>
                  </label>
                ))
              )}
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setGroupsSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={savingGroups} onClick={() => void saveGroups()}>
              {savingGroups ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.permission_groups.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>

      {/* Create User Sheet */}
      <Sheet open={createSheetOpen} onOpenChange={setCreateSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>{t("dashboard.users.sheet_create_title", "Create User")}</SheetTitle>
            <SheetDescription>
              {t("dashboard.users.sheet_create_desc", "Add a new system user.")}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="user-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="user-name" value={createForm.name} onChange={(e) => setCreateForm((f) => ({ ...f, name: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="user-email">{t("dashboard.users.col_email", "Email")}</Label>
              <Input id="user-email" type="email" value={createForm.email} onChange={(e) => setCreateForm((f) => ({ ...f, email: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="user-password">{t("dashboard.auth.password", "Password")}</Label>
              <Input id="user-password" type="password" value={createForm.password} onChange={(e) => setCreateForm((f) => ({ ...f, password: e.target.value }))} />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setCreateSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={creating} onClick={() => void handleCreate()}>
              {creating ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.users.create", "Create User")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.users.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.users.confirm_delete", "Delete this user?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
