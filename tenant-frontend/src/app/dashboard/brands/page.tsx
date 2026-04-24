"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listBrands, createBrand, updateBrand, deleteBrand } from "@/lib/tenant-resources";

type Brand = { id: number; name: string; slug?: string; domain?: string; is_active?: boolean };

const config: SimpleCRUDConfig<Brand> = {
  titleKey: "dashboard.brands.title",
  titleFallback: "Brands",
  subtitleKey: "dashboard.brands.subtitle",
  subtitleFallback: "Manage your brands",
  createLabelKey: "dashboard.brands.create",
  createLabelFallback: "New Brand",
  fields: [
    { name: "name", label: "Name", placeholder: "Acme Corp", required: true },
    { name: "slug", label: "Slug", placeholder: "acme-corp" },
    { name: "domain", label: "Domain", type: "url", placeholder: "https://acme.com" },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listBrands as () => Promise<Brand[]>,
  createFn: createBrand,
  updateFn: updateBrand,
  deleteFn: deleteBrand as unknown as (id: number) => Promise<void>,
  columns: (t): Array<ColumnDef<Brand>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
    { accessorKey: "slug", header: t("dashboard.table.slug", "Slug") },
    { accessorKey: "domain", header: t("dashboard.table.domain", "Domain") },
  ],
  toForm: (r) => ({ name: r.name, slug: r.slug ?? "", domain: r.domain ?? "", is_active: r.is_active ? "1" : "0" }),
  fromForm: (f) => ({ name: f.name, slug: f.slug || undefined, domain: f.domain || undefined, is_active: f.is_active === "1" }),
};

export default function BrandsPage() {
  return <SimpleCRUDPage config={config} />;
}
