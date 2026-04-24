"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listBranches, createBranch, updateBranch, deleteBranch } from "@/lib/tenant-resources";

type Branch = { id: number; name: string; slug?: string; brand_id?: number; is_active?: boolean };

const config: SimpleCRUDConfig<Branch> = {
  titleKey: "dashboard.branches.title",
  titleFallback: "Branches",
  subtitleKey: "dashboard.branches.subtitle",
  subtitleFallback: "Manage your branches",
  createLabelKey: "dashboard.branches.create",
  createLabelFallback: "New Branch",
  fields: [
    { name: "name", label: "Name", placeholder: "Main Branch", required: true },
    { name: "slug", label: "Slug", placeholder: "main-branch" },
    { name: "brand_id", label: "Brand ID", type: "number", placeholder: "1" },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listBranches as () => Promise<Branch[]>,
  createFn: createBranch,
  updateFn: updateBranch,
  deleteFn: deleteBranch as unknown as (id: number) => Promise<void>,
  columns: (t): Array<ColumnDef<Branch>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
    { accessorKey: "slug", header: t("dashboard.table.slug", "Slug") },
    { accessorKey: "brand_id", header: t("dashboard.table.brand", "Brand") },
  ],
  toForm: (r) => ({ name: r.name, slug: r.slug ?? "", brand_id: r.brand_id?.toString() ?? "", is_active: r.is_active ? "1" : "0" }),
  fromForm: (f) => ({ name: f.name, slug: f.slug || undefined, brand_id: f.brand_id ? Number(f.brand_id) : undefined, is_active: f.is_active === "1" }),
};

export default function BranchesPage() {
  return <SimpleCRUDPage config={config} />;
}
