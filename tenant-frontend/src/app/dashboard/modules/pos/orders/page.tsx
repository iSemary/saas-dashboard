"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listSalesOrders, deleteSalesOrder } from "@/lib/tenant-resources";

type PosOrder = {
  id: number;
  barcode?: string;
  total_price: number;
  amount_paid: number;
  pay_method: string;
  order_type: string;
  status: string;
  tax?: number;
  created_at?: string;
};

const config: SimpleCRUDConfig<PosOrder> = {
  titleKey: "dashboard.pos.orders",
  titleFallback: "POS Orders",
  subtitleKey: "dashboard.pos.orders_subtitle",
  subtitleFallback: "View point-of-sale transactions",
  createLabelKey: "dashboard.pos.create_order",
  createLabelFallback: "New Order",
  fields: [],
  listFn: listSalesOrders as () => Promise<PosOrder[]>,
  createFn: async () => { throw new Error("Use the Sales module to create orders."); },
  deleteFn: deleteSalesOrder as unknown as (id: number) => Promise<void>,
  moduleKey: "pos",
  dashboardHref: "/dashboard/modules/pos",
  columns: (t): Array<ColumnDef<PosOrder>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "barcode", header: t("dashboard.pos.barcode", "Barcode") },
    {
      accessorKey: "total_price",
      header: t("dashboard.sales.total", "Total"),
      cell: ({ row }) => `$${Number(row.original.total_price).toFixed(2)}`,
    },
    {
      accessorKey: "amount_paid",
      header: t("dashboard.sales.paid", "Paid"),
      cell: ({ row }) => `$${Number(row.original.amount_paid).toFixed(2)}`,
    },
    { accessorKey: "pay_method", header: t("dashboard.sales.method", "Method") },
    { accessorKey: "order_type", header: t("dashboard.sales.type", "Type") },
    {
      accessorKey: "status",
      header: t("dashboard.table.status", "Status"),
      cell: ({ row }) => {
        const s = row.original.status;
        const color = s === "completed" ? "text-green-600" : s === "cancelled" ? "text-red-500" : "text-yellow-600";
        return <span className={`font-medium capitalize ${color}`}>{s}</span>;
      },
    },
    { accessorKey: "created_at", header: t("dashboard.table.date", "Date") },
  ],
  toForm: (r) => ({ status: r.status }),
  fromForm: (f) => ({ status: f.status }),
};

export default function PosOrdersPage() {
  return <SimpleCRUDPage config={config} />;
}
