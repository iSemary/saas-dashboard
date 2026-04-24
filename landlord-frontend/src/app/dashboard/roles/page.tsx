"use client";

import { useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, UsersRound } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { listPermissionGroups, syncRolePermissionGroups, type PermissionGroup } from "@/lib/permission-groups";
import { listRoles } from "@/lib/resources";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { useI18n } from "@/context/i18n-context";

type RoleRow = {
  id: number;
  name: string;
  guard_name?: string;
  permission_groups?: Array<{ id: number; name: string; slug: string }>;
  permissions_count?: number;
};

export default function RolesPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<RoleRow[]>([]);
  const [groupsSheetOpen, setGroupsSheetOpen] = useState(false);
  const [groupsSheetRole, setGroupsSheetRole] = useState<RoleRow | null>(null);
  const [allGroups, setAllGroups] = useState<PermissionGroup[]>([]);
  const [selectedGroupIds, setSelectedGroupIds] = useState<Set<number>>(new Set());
  const [savingGroups, setSavingGroups] = useState(false);

  useEffect(() => {
    listRoles().then((r) => setRows(r as RoleRow[])).catch(() => setRows([]));
  }, []);

  const openGroups = async (role: RoleRow) => {
    setGroupsSheetRole(role);
    setGroupsSheetOpen(true);
    try {
      const g = await listPermissionGroups();
      setAllGroups(g);
      setSelectedGroupIds(new Set((role.permission_groups ?? []).map((x) => x.id)));
    } catch {
      toast.error(t("dashboard.roles.toast_groups_load_error", "Could not load permission groups."));
      setAllGroups([]);
    }
  };

  const saveGroups = async () => {
    if (!groupsSheetRole) return;
    setSavingGroups(true);
    try {
      await syncRolePermissionGroups(groupsSheetRole.id, [...selectedGroupIds]);
      toast.success(t("dashboard.roles.toast_groups_ok", "Permission groups updated."));
      setGroupsSheetOpen(false);
      const data = await listRoles();
      setRows(data as RoleRow[]);
    } catch {
      toast.error(t("dashboard.roles.toast_groups_error", "Could not save."));
    } finally {
      setSavingGroups(false);
    }
  };

  const columns: Array<ColumnDef<RoleRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "guard_name", header: t("dashboard.permissions.guard_name", "Guard") },
      {
        id: "permissions_count",
        header: t("dashboard.roles.total_permissions", "Permissions"),
        cell: ({ row }) => row.original.permissions_count ?? "—",
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <Button type="button" variant="outline" size="sm" className="h-8 gap-1" onClick={() => void openGroups(row.original)}>
            <UsersRound className="size-3.5" />
            {t("dashboard.users.groups_button", "Groups")}
          </Button>
        ),
      },
    ],
    [t],
  );

  return (
    <div className="space-y-3">
      <h1 className="text-xl font-semibold">{t("dashboard.roles.title", "Roles")}</h1>
      <p className="text-sm text-muted-foreground">
        {t(
          "dashboard.roles.subtitle",
          "Roles carry direct permissions; permission groups add bundled permissions on top.",
        )}
      </p>
      <DataTable columns={columns} data={rows} />
      <Sheet open={groupsSheetOpen} onOpenChange={setGroupsSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>{t("dashboard.roles.sheet_title", "Permission groups")}</SheetTitle>
            <SheetDescription>
              {groupsSheetRole
                ? `${t("dashboard.roles.sheet_assign", "Assign groups for role")} "${groupsSheetRole.name}"`
                : ""}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-3 overflow-y-auto px-4 pb-4">
            <div className="max-h-64 space-y-2 overflow-y-auto rounded-md border border-border/80 p-3">
              {allGroups.length === 0 ? (
                <p className="text-sm text-muted-foreground">No groups defined.</p>
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
    </div>
  );
}
