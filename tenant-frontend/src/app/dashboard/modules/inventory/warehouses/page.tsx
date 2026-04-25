"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listWarehouses, createWarehouse, updateWarehouse, deleteWarehouse } from "@/lib/tenant-resources";

type WarehouseItem = { id: number; name: string; code: string; is_active: boolean; is_default: boolean; city?: string; stock_moves_count?: number };

const config: SimpleCRUDConfig<WarehouseItem> = {
  titleKey: "dashboard.inventory.warehouses",
  titleFallback: "Warehouses",
  subtitleKey: "dashboard.inventory.warehouses_subtitle",
  subtitleFallback: "Manage warehouse locations",
  createLabelKey: "dashboard.inventory.create_warehouse",
  createLabelFallback: "New Warehouse",
  fields: [
    { name: "name", label: "Name", placeholder: "Main Warehouse", required: true },
    { name: "code", label: "Code", placeholder: "WH-001", required: true },
    { name: "address", label: "Address", placeholder: "123 Storage St" },
    { name: "city", label: "City", placeholder: "Cairo" },
    { name: "phone", label: "Phone", placeholder: "+20..." },
  ],
  listFn: listWarehouses as () => Promise<WarehouseItem[]>,
  createFn: createWarehouse,
  updateFn: updateWarehouse,
  deleteFn: deleteWarehouse as unknown as (id: number) => Promise<void>,
  moduleKey: "inventory",
  dashboardHref: "/dashboard/modules/inventory",
  columns: (t): Array<ColumnDef<WarehouseItem>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.table.name", "Name") },
    { accessorKey: "code", header: t("dashboard.inventory.code", "Code") },
    { accessorKey: "city", header: t("dashboard.inventory.city", "City") },
    {
      accessorKey: "is_active",
      header: t("dashboard.table.active", "Active"),
      cell: ({ row }) => (row.original.is_active ? "✓" : "—"),
    },
    {
      accessorKey: "is_default",
      header: t("dashboard.inventory.default", "Default"),
      cell: ({ row }) => (row.original.is_default ? "★" : "—"),
    },
    { accessorKey: "stock_moves_count", header: t("dashboard.inventory.moves", "Moves") },
  ],
  toForm: (r) => ({ name: r.name, code: r.code, city: r.city ?? "" }),
  fromForm: (f) => ({ name: f.name, code: f.code, address: f.address, city: f.city, phone: f.phone }),
};

export default function WarehousesPage() {
  return <SimpleCRUDPage config={config} />;
}
