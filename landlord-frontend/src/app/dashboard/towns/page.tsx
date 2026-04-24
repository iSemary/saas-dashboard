"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listTowns, createTown, deleteTown, type TownRow } from "@/lib/resources";

const config: SimpleCRUDConfig<TownRow> = {
  titleKey: "dashboard.towns.title",
  titleFallback: "Towns",
  subtitleKey: "dashboard.towns.subtitle",
  subtitleFallback: "Manage towns within cities.",
  createLabelKey: "dashboard.towns.create",
  createLabelFallback: "Add Town",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "city_id", label: "City ID", type: "number", required: true },
  ],
  listFn: listTowns,
  createFn: createTown,
  deleteFn: deleteTown,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    {
      id: "city",
      header: t("dashboard.towns.city", "City"),
      cell: ({ row }: { row: { original: TownRow } }) => row.original.city?.name ?? "—",
    },
  ],
  toForm: (row) => ({ name: row.name, city_id: String(row.city_id) }),
  fromForm: (form) => ({ ...form, city_id: Number(form.city_id) }),
};

export default function TownsPage() {
  return <SimpleCRUDPage config={config} />;
}
