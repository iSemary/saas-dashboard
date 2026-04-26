"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listPosOfferPrices,
  createPosOfferPrice,
  updatePosOfferPrice,
  deletePosOfferPrice,
} from "@/lib/tenant-resources";

type OfferPrice = {
  id: number;
  product_id: number;
  product?: { name: string };
  amount: number;
  buyer_name?: string;
  reduce_stock: boolean;
  created_at?: string;
};

const config: SimpleCRUDConfig<OfferPrice> = {
  titleKey: "dashboard.pos.offer_prices",
  titleFallback: "Offer Prices",
  subtitleKey: "dashboard.pos.offer_prices_subtitle",
  subtitleFallback: "Manage special pricing and promotions",
  createLabelKey: "dashboard.pos.create_offer_price",
  createLabelFallback: "New Offer Price",
  fields: [
    { name: "product_id", label: "Product ID", type: "number", placeholder: "e.g., 1", required: true },
    { name: "amount", label: "Offer Price", type: "number", placeholder: "0.00", required: true },
    { name: "buyer_name", label: "Buyer Name", placeholder: "Optional buyer name" },
    {
      name: "reduce_stock",
      label: "Reduce Stock",
      type: "select",
      options: [
        { value: "true", label: "Yes" },
        { value: "false", label: "No" },
      ],
    },
  ],
  listFn: listPosOfferPrices as () => Promise<OfferPrice[]>,
  createFn: createPosOfferPrice,
  updateFn: updatePosOfferPrice,
  deleteFn: deletePosOfferPrice as unknown as (id: number) => Promise<void>,
  moduleKey: "pos",
  dashboardHref: "/dashboard/modules/pos",
  columns: (t): Array<ColumnDef<OfferPrice>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    {
      accessorKey: "product",
      header: t("dashboard.pos.product", "Product"),
      cell: ({ row }) => row.original.product?.name ?? "—",
    },
    {
      accessorKey: "amount",
      header: t("dashboard.pos.offer_price", "Offer Price"),
      cell: ({ row }) => `$${Number(row.original.amount).toFixed(2)}`,
    },
    { accessorKey: "buyer_name", header: t("dashboard.pos.buyer", "Buyer") },
    {
      accessorKey: "reduce_stock",
      header: t("dashboard.pos.reduce_stock", "Reduce Stock"),
      cell: ({ row }) => (row.original.reduce_stock ? "Yes" : "No"),
    },
  ],
  toForm: (r) => ({
    product_id: String(r.product_id ?? ""),
    amount: String(r.amount ?? ""),
    buyer_name: r.buyer_name ?? "",
    reduce_stock: String(r.reduce_stock ?? false),
  }),
  fromForm: (f) => ({
    product_id: Number(f.product_id),
    amount: Number(f.amount),
    buyer_name: f.buyer_name || undefined,
    reduce_stock: f.reduce_stock === "true",
  }),
};

export default function PosOfferPricesPage() {
  return <SimpleCRUDPage config={config} />;
}
