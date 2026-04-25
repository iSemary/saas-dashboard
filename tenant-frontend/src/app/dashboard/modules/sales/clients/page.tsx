"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listSalesClients,
  createSalesClient,
  updateSalesClient,
  deleteSalesClient,
} from "@/lib/tenant-resources";

type SalesClient = {
  id: number;
  code?: string;
  phone?: string;
  address?: string;
  gift?: number;
  user?: { name: string; email: string };
  created_at?: string;
};

const config: SimpleCRUDConfig<SalesClient> = {
  titleKey: "dashboard.sales.clients",
  titleFallback: "Sales Clients",
  subtitleKey: "dashboard.sales.clients_subtitle",
  subtitleFallback: "Manage loyal customers and clients",
  createLabelKey: "dashboard.sales.create_client",
  createLabelFallback: "New Client",
  fields: [
    { name: "user_id", label: "User ID", type: "number", placeholder: "e.g. 5", required: true },
    { name: "code", label: "Client Code", placeholder: "e.g. CLT-001" },
    { name: "phone", label: "Phone", placeholder: "+20..." },
    { name: "address", label: "Address", placeholder: "Street, City" },
    { name: "gift", label: "Gift Balance", type: "number", placeholder: "0.00" },
  ],
  listFn: listSalesClients as () => Promise<SalesClient[]>,
  createFn: createSalesClient,
  updateFn: updateSalesClient,
  deleteFn: deleteSalesClient as unknown as (id: number) => Promise<void>,
  moduleKey: "sales",
  dashboardHref: "/dashboard/modules/sales",
  columns: (t): Array<ColumnDef<SalesClient>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    {
      accessorKey: "user",
      header: t("dashboard.table.name", "Name"),
      cell: ({ row }) => row.original.user?.name ?? "—",
    },
    {
      accessorKey: "email",
      header: t("dashboard.table.email", "Email"),
      cell: ({ row }) => row.original.user?.email ?? "—",
    },
    { accessorKey: "code", header: t("dashboard.sales.code", "Code") },
    { accessorKey: "phone", header: t("dashboard.sales.phone", "Phone") },
    {
      accessorKey: "gift",
      header: t("dashboard.sales.gift", "Gift Balance"),
      cell: ({ row }) => `$${Number(row.original.gift ?? 0).toFixed(2)}`,
    },
    { accessorKey: "created_at", header: t("dashboard.table.date", "Joined") },
  ],
  toForm: (r) => ({
    user_id: "",
    code: r.code ?? "",
    phone: r.phone ?? "",
    address: r.address ?? "",
    gift: String(r.gift ?? "0"),
  }),
  fromForm: (f) => ({
    user_id: Number(f.user_id),
    code: f.code,
    phone: f.phone,
    address: f.address,
    gift: f.gift ? Number(f.gift) : undefined,
  }),
};

export default function SalesClientsPage() {
  return <SimpleCRUDPage config={config} />;
}
