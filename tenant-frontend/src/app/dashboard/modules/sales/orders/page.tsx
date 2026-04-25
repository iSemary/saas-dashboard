"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listSalesOrders, createSalesOrder, deleteSalesOrder } from "@/lib/tenant-resources";

type SalesOrder = {
  id: number;
  barcode?: string;
  total_price: number;
  amount_paid: number;
  pay_method: string;
  order_type: string;
  status: string;
  created_at?: string;
};

const config: SimpleCRUDConfig<SalesOrder> = {
  titleKey: "dashboard.sales.orders",
  titleFallback: "Sales Orders",
  subtitleKey: "dashboard.sales.orders_subtitle",
  subtitleFallback: "View and manage sales transactions",
  createLabelKey: "dashboard.sales.create_order",
  createLabelFallback: "New Order",
  fields: [
    { name: "total_price", label: "Total Price", type: "number", placeholder: "0.00", required: true },
    { name: "amount_paid", label: "Amount Paid", type: "number", placeholder: "0.00", required: true },
    { name: "pay_method", label: "Payment Method", placeholder: "cash / card / installment", required: true },
    { name: "order_type", label: "Order Type", placeholder: "takeaway / dine_in / delivery / steward", required: true },
  ],
  listFn: listSalesOrders as () => Promise<SalesOrder[]>,
  createFn: createSalesOrder,
  deleteFn: deleteSalesOrder as unknown as (id: number) => Promise<void>,
  moduleKey: "sales",
  dashboardHref: "/dashboard/modules/sales",
  columns: (t): Array<ColumnDef<SalesOrder>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "barcode", header: t("dashboard.sales.barcode", "Barcode") },
    {
      accessorKey: "total_price",
      header: t("dashboard.sales.total", "Total"),
      cell: ({ row }) => `$${Number(row.original.total_price).toFixed(2)}`,
    },
    { accessorKey: "pay_method", header: t("dashboard.sales.method", "Method") },
    { accessorKey: "order_type", header: t("dashboard.sales.type", "Type") },
    { accessorKey: "status", header: t("dashboard.table.status", "Status") },
    { accessorKey: "created_at", header: t("dashboard.table.date", "Date") },
  ],
  toForm: (r) => ({
    total_price: String(r.total_price),
    amount_paid: String(r.amount_paid),
    pay_method: r.pay_method,
    order_type: r.order_type,
  }),
  fromForm: (f) => ({
    total_price: Number(f.total_price),
    amount_paid: Number(f.amount_paid),
    pay_method: f.pay_method,
    order_type: f.order_type,
    products: [],
  }),
};

export default function SalesOrdersPage() {
  return <SimpleCRUDPage config={config} />;
}
