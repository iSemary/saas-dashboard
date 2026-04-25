"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listPosSubCategories,
  createPosSubCategory,
  updatePosSubCategory,
  deletePosSubCategory,
} from "@/lib/tenant-resources";

type SubCategory = { id: number; name: string; category?: { name: string }; products_count?: number };

const config: SimpleCRUDConfig<SubCategory> = {
  titleKey: "dashboard.pos.sub_categories",
  titleFallback: "Sub-Categories",
  subtitleKey: "dashboard.pos.sub_categories_subtitle",
  subtitleFallback: "Manage product sub-categories",
  createLabelKey: "dashboard.pos.create_sub_category",
  createLabelFallback: "New Sub-Category",
  fields: [
    { name: "name", label: "Name", placeholder: "Sub-category name", required: true },
    { name: "category_id", label: "Category ID", type: "number", placeholder: "e.g. 1", required: true },
  ],
  listFn: listPosSubCategories as () => Promise<SubCategory[]>,
  createFn: createPosSubCategory,
  updateFn: updatePosSubCategory,
  deleteFn: deletePosSubCategory as unknown as (id: number) => Promise<void>,
  moduleKey: "pos",
  dashboardHref: "/dashboard/modules/pos",
  columns: (t): Array<ColumnDef<SubCategory>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
    {
      accessorKey: "category",
      header: t("dashboard.table.category", "Category"),
      cell: ({ row }) => row.original.category?.name ?? "—",
    },
    { accessorKey: "products_count", header: t("dashboard.pos.products_count", "Products") },
  ],
  toForm: (r) => ({ name: r.name, category_id: String(r.category?.name ?? "") }),
  fromForm: (f) => ({ name: f.name, category_id: Number(f.category_id) }),
};

export default function PosSubCategoriesPage() {
  return <SimpleCRUDPage config={config} />;
}
