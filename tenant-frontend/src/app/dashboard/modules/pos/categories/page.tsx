"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listPosCategories,
  createPosCategory,
  updatePosCategory,
  deletePosCategory,
} from "@/lib/tenant-resources";

type Category = { id: number; name: string; products_count?: number; created_at?: string };

const config: SimpleCRUDConfig<Category> = {
  titleKey: "dashboard.pos.categories",
  titleFallback: "Categories",
  subtitleKey: "dashboard.pos.categories_subtitle",
  subtitleFallback: "Manage product categories",
  createLabelKey: "dashboard.pos.create_category",
  createLabelFallback: "New Category",
  fields: [
    { name: "name", label: "Name", placeholder: "Category name", required: true },
  ],
  listFn: listPosCategories as () => Promise<Category[]>,
  createFn: createPosCategory,
  updateFn: updatePosCategory,
  deleteFn: deletePosCategory as unknown as (id: number) => Promise<void>,
  moduleKey: "pos",
  dashboardHref: "/dashboard/modules/pos",
  columns: (t): Array<ColumnDef<Category>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
    { accessorKey: "products_count", header: t("dashboard.pos.products_count", "Products") },
  ],
  toForm: (r) => ({ name: r.name }),
  fromForm: (f) => ({ name: f.name }),
};

export default function PosCategoriesPage() {
  return <SimpleCRUDPage config={config} />;
}
