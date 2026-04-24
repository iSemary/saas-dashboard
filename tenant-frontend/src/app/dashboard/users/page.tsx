"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listUsers, createUser, updateUser, deleteUser } from "@/lib/tenant-resources";

type User = { id: number; name: string; email: string; created_at?: string };

const config: SimpleCRUDConfig<User> = {
  titleKey: "dashboard.users.title",
  titleFallback: "Users",
  subtitleKey: "dashboard.users.subtitle",
  subtitleFallback: "Manage tenant users",
  createLabelKey: "dashboard.users.create",
  createLabelFallback: "New User",
  fields: [
    { name: "name", label: "Name", placeholder: "John Doe", required: true },
    { name: "email", label: "Email", type: "email", placeholder: "john@example.com", required: true },
    { name: "password", label: "Password", type: "password", placeholder: "••••••••", required: true },
  ],
  listFn: listUsers as () => Promise<User[]>,
  createFn: createUser,
  updateFn: updateUser,
  deleteFn: deleteUser as unknown as (id: number) => Promise<void>,
  columns: (t): Array<ColumnDef<User>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
    { accessorKey: "email", header: t("dashboard.table.email", "Email") },
  ],
  toForm: (r) => ({ name: r.name, email: r.email, password: "" }),
  fromForm: (f) => {
    const p: Record<string, unknown> = { name: f.name, email: f.email };
    if (f.password) p.password = f.password;
    return p;
  },
};

export default function UsersPage() {
  return <SimpleCRUDPage config={config} />;
}
