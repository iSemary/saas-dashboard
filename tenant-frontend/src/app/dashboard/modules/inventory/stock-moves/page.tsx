"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listStockMoves, createStockMove, deleteStockMove } from "@/lib/tenant-resources";

type StockMove = {
  id: number;
  reference?: string;
  product_id: number;
  warehouse?: { name: string };
  move_type: string;
  quantity: number;
  state: string;
  date?: string;
};

const config: SimpleCRUDConfig<StockMove> = {
  titleKey: "dashboard.inventory.stock_moves",
  titleFallback: "Stock Moves",
  subtitleKey: "dashboard.inventory.stock_moves_subtitle",
  subtitleFallback: "Track product inbound and outbound movements",
  createLabelKey: "dashboard.inventory.create_move",
  createLabelFallback: "New Stock Move",
  fields: [
    { name: "product_id", label: "Product ID", type: "number", placeholder: "e.g. 5", required: true },
    { name: "warehouse_id", label: "Warehouse ID", type: "number", placeholder: "e.g. 1", required: true },
    { name: "move_type", label: "Move Type", placeholder: "in / out / transfer / adjust", required: true },
    { name: "quantity", label: "Quantity", type: "number", placeholder: "0.01", required: true },
    { name: "unit_cost", label: "Unit Cost", type: "number", placeholder: "0.00" },
    { name: "date", label: "Date", type: "text" },
    { name: "description", label: "Description", placeholder: "Optional notes" },
  ],
  listFn: listStockMoves as () => Promise<StockMove[]>,
  createFn: createStockMove,
  deleteFn: deleteStockMove as unknown as (id: number) => Promise<void>,
  moduleKey: "inventory",
  dashboardHref: "/dashboard/modules/inventory",
  columns: (t): Array<ColumnDef<StockMove>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "reference", header: t("dashboard.inventory.reference", "Reference") },
    { accessorKey: "product_id", header: t("dashboard.pos.product", "Product ID") },
    {
      accessorKey: "warehouse",
      header: t("dashboard.inventory.warehouse", "Warehouse"),
      cell: ({ row }) => row.original.warehouse?.name ?? "—",
    },
    { accessorKey: "move_type", header: t("dashboard.inventory.type", "Type") },
    { accessorKey: "quantity", header: t("dashboard.inventory.quantity", "Qty") },
    { accessorKey: "state", header: t("dashboard.table.status", "State") },
    { accessorKey: "date", header: t("dashboard.table.date", "Date") },
  ],
  toForm: (r) => ({
    product_id: String(r.product_id),
    warehouse_id: "",
    move_type: r.move_type,
    quantity: String(r.quantity),
  }),
  fromForm: (f) => ({
    product_id: Number(f.product_id),
    warehouse_id: Number(f.warehouse_id),
    move_type: f.move_type,
    quantity: Number(f.quantity),
    unit_cost: f.unit_cost ? Number(f.unit_cost) : undefined,
    date: f.date,
    description: f.description,
  }),
};

export default function StockMovesPage() {
  return <SimpleCRUDPage config={config} />;
}
