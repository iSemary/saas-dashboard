"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listPosProducts,
  createPosProduct,
  updatePosProduct,
  deletePosProduct,
} from "@/lib/tenant-resources";

type Product = {
  id: number;
  name: string;
  purchase_price: number;
  sale_price: number;
  amount: number;
  is_offer: boolean;
  category?: { name: string };
  created_at?: string;
};

const config: SimpleCRUDConfig<Product> = {
  titleKey: "dashboard.pos.products",
  titleFallback: "Products",
  subtitleKey: "dashboard.pos.products_subtitle",
  subtitleFallback: "Manage POS products",
  createLabelKey: "dashboard.pos.create_product",
  createLabelFallback: "New Product",
  fields: [
    { name: "name", label: "Name", placeholder: "Product name", required: true },
    { name: "purchase_price", label: "Purchase Price", type: "number", placeholder: "0.00", required: true },
    { name: "sale_price", label: "Sale Price", type: "number", placeholder: "0.00", required: true },
    { name: "amount", label: "Stock Amount", type: "number", placeholder: "0", required: true },
    { name: "description", label: "Description", placeholder: "Optional description" },
    { name: "barcode_number", label: "Barcode Number", placeholder: "Auto-generated if empty" },
  ],
  listFn: listPosProducts as () => Promise<Product[]>,
  createFn: createPosProduct,
  updateFn: updatePosProduct,
  deleteFn: deletePosProduct as unknown as (id: number) => Promise<void>,
  moduleKey: "pos",
  dashboardHref: "/dashboard/modules/pos",
  columns: (t): Array<ColumnDef<Product>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
    {
      accessorKey: "category",
      header: t("dashboard.table.category", "Category"),
      cell: ({ row }) => row.original.category?.name ?? "—",
    },
    {
      accessorKey: "purchase_price",
      header: t("dashboard.pos.purchase_price", "Purchase"),
      cell: ({ row }) => `$${Number(row.original.purchase_price).toFixed(2)}`,
    },
    {
      accessorKey: "sale_price",
      header: t("dashboard.pos.sale_price", "Sale"),
      cell: ({ row }) => `$${Number(row.original.sale_price).toFixed(2)}`,
    },
    { accessorKey: "amount", header: t("dashboard.pos.stock", "Stock") },
    {
      accessorKey: "is_offer",
      header: t("dashboard.pos.offer", "Offer"),
      cell: ({ row }) => (row.original.is_offer ? "✓" : "—"),
    },
  ],
  toForm: (r) => ({
    name: r.name,
    purchase_price: String(r.purchase_price),
    sale_price: String(r.sale_price),
    amount: String(r.amount),
  }),
  fromForm: (f) => ({
    name: f.name,
    purchase_price: Number(f.purchase_price),
    sale_price: Number(f.sale_price),
    amount: Number(f.amount),
    description: f.description,
    barcode_number: f.barcode_number,
  }),
};

export default function PosProductsPage() {
  return <SimpleCRUDPage config={config} />;
}
