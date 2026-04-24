"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listPermissions, createPermission, updatePermission, deletePermission } from "@/lib/tenant-resources";

type Permission = { id: number; name: string; guard_name?: string };

const config: SimpleCRUDConfig<Permission> = {
  titleKey: "dashboard.permissions.title",
  titleFallback: "Permissions",
  subtitleKey: "dashboard.permissions.subtitle",
  subtitleFallback: "Manage tenant permissions",
  createLabelKey: "dashboard.permissions.create",
  createLabelFallback: "New Permission",
  fields: [
    { name: "name", label: "Name", placeholder: "view.users", required: true },
    { name: "guard_name", label: "Guard", placeholder: "web" },
  ],
  listFn: listPermissions as () => Promise<Permission[]>,
  createFn: createPermission,
  updateFn: updatePermission,
  deleteFn: deletePermission as unknown as (id: number) => Promise<void>,
  columns: (t): Array<ColumnDef<Permission>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
  ],
  toForm: (r) => ({ name: r.name, guard_name: r.guard_name ?? "" }),
  fromForm: (f) => ({ name: f.name, guard_name: f.guard_name || undefined }),
};

export default function PermissionsPage() {
  return <SimpleCRUDPage config={config} />;
}
