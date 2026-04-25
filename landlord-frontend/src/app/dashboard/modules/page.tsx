"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { Badge } from "@/components/ui/badge";
import {
  listModules,
  createModule,
  updateModule,
  type ModuleRow,
} from "@/lib/resources";

const config: SimpleCRUDConfig<ModuleRow> = {
  titleKey: "dashboard.modules.title",
  titleFallback: "Modules",
  subtitleKey: "dashboard.modules.subtitle",
  subtitleFallback: "Manage platform modules and their navigation.",
  createLabelKey: "dashboard.modules.create",
  createLabelFallback: "New Module",
  fields: [
    { name: "module_key", label: "Module Key", placeholder: "e.g. crm", required: true },
    { name: "name", label: "Name", placeholder: "e.g. CRM", required: true },
    { name: "description", label: "Description", type: "textarea", placeholder: "Module description" },
    { name: "route", label: "Route", placeholder: "e.g. /dashboard/modules/crm" },
    { name: "icon", label: "Icon", placeholder: "e.g. crm.png" },
    { name: "slogan", label: "Slogan", placeholder: "e.g. One call, one deal" },
    {
      name: "navigation",
      label: "Navigation Items",
      type: "navItems",
    },
    {
      name: "status",
      label: "Status",
      type: "select",
      options: [
        { value: "active", label: "Active" },
        { value: "inactive", label: "Inactive" },
      ],
    },
  ],
  listFn: listModules as () => Promise<ModuleRow[]>,
  createFn: createModule,
  updateFn: updateModule,
  columns: (t): Array<ColumnDef<ModuleRow>> => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "module_key", header: t("dashboard.modules.slug", "Key") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    {
      accessorKey: "status",
      header: t("dashboard.modules.status", "Status"),
      cell: ({ row }) => (
        <Badge variant={row.original.status === "active" ? "default" : "secondary"}>
          {row.original.status === "active" ? "Active" : "Inactive"}
        </Badge>
      ),
    },
    {
      accessorKey: "navigation",
      header: t("dashboard.modules.nav_items", "Nav Items"),
      cell: ({ row }) => {
        const nav = row.original.navigation;
        return nav ? `${nav.length} items` : "—";
      },
    },
  ],
  toForm: (r) => ({
    module_key: r.module_key ?? "",
    name: r.name ?? "",
    description: r.description ?? "",
    route: r.route ?? "",
    icon: r.icon ?? "",
    slogan: r.slogan ?? "",
    navigation: JSON.stringify(r.navigation ?? []),
    status: r.status ?? "active",
  }),
  fromForm: (f) => {
    let navigation = null;
    try {
      const parsed = JSON.parse(f.navigation || "[]");
      if (Array.isArray(parsed) && parsed.length > 0) {
        navigation = parsed.filter((item: Record<string, string>) => item.key && item.label && item.route);
      }
    } catch { /* keep null */ }
    return {
      module_key: f.module_key,
      name: f.name,
      description: f.description || null,
      route: f.route || null,
      icon: f.icon || null,
      slogan: f.slogan || null,
      navigation,
      status: f.status || "active",
    };
  },
};

export default function ModulesPage() {
  return <SimpleCRUDPage config={config} />;
}
