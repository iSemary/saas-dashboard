"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listPosDamaged, createPosDamaged, deletePosDamaged } from "@/lib/tenant-resources";

type Damaged = { id: number; product_id: number; product?: { name: string }; amount: number; created_at?: string };

const config: SimpleCRUDConfig<Damaged> = {
  titleKey: "dashboard.pos.damaged",
  titleFallback: "Damaged Records",
  subtitleKey: "dashboard.pos.damaged_subtitle",
  subtitleFallback: "Track damaged product inventory",
  createLabelKey: "dashboard.pos.create_damaged",
  createLabelFallback: "Record Damage",
  fields: [
    { name: "product_id", label: "Product ID", type: "number", placeholder: "e.g. 5", required: true },
    { name: "amount", label: "Quantity Damaged", type: "number", placeholder: "0.01", required: true },
  ],
  listFn: listPosDamaged as () => Promise<Damaged[]>,
  createFn: createPosDamaged,
  deleteFn: deletePosDamaged as unknown as (id: number) => Promise<void>,
  moduleKey: "pos",
  dashboardHref: "/dashboard/modules/pos",
  columns: (t): Array<ColumnDef<Damaged>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    {
      accessorKey: "product",
      header: t("dashboard.pos.product", "Product"),
      cell: ({ row }) => row.original.product?.name ?? String(row.original.product_id),
    },
    { accessorKey: "amount", header: t("dashboard.pos.amount", "Quantity") },
    { accessorKey: "created_at", header: t("dashboard.table.date", "Date") },
  ],
  toForm: (r) => ({ product_id: String(r.product_id), amount: String(r.amount) }),
  fromForm: (f) => ({ product_id: Number(f.product_id), amount: Number(f.amount) }),
};

export default function PosDamagedPage() {
  return <SimpleCRUDPage config={config} />;
}
