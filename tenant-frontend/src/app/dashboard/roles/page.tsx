"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listRoles, createRole, updateRole, deleteRole } from "@/lib/tenant-resources";

type Role = { id: number; name: string; guard_name?: string; created_at?: string };

const config: SimpleCRUDConfig<Role> = {
  titleKey: "dashboard.roles.title",
  titleFallback: "Roles",
  subtitleKey: "dashboard.roles.subtitle",
  subtitleFallback: "Manage tenant roles",
  createLabelKey: "dashboard.roles.create",
  createLabelFallback: "New Role",
  fields: [
    { name: "name", label: "Name", placeholder: "admin", required: true },
    { name: "guard_name", label: "Guard", placeholder: "web" },
  ],
  listFn: listRoles as () => Promise<Role[]>,
  createFn: createRole,
  updateFn: updateRole,
  deleteFn: deleteRole as unknown as (id: number) => Promise<void>,
  columns: (t): Array<ColumnDef<Role>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
  ],
  toForm: (r) => ({ name: r.name, guard_name: r.guard_name ?? "" }),
  fromForm: (f) => ({ name: f.name, guard_name: f.guard_name || undefined }),
};

export default function RolesPage() {
  return <SimpleCRUDPage config={config} />;
}
