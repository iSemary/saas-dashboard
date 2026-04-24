"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listStreets, createStreet, deleteStreet, type StreetRow } from "@/lib/resources";

const config: SimpleCRUDConfig<StreetRow> = {
  titleKey: "dashboard.streets.title",
  titleFallback: "Streets",
  subtitleKey: "dashboard.streets.subtitle",
  subtitleFallback: "Manage streets within towns.",
  createLabelKey: "dashboard.streets.create",
  createLabelFallback: "Add Street",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "town_id", label: "Town ID", type: "number", required: true },
  ],
  listFn: listStreets,
  createFn: createStreet,
  deleteFn: deleteStreet,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    {
      id: "town",
      header: t("dashboard.streets.town", "Town"),
      cell: ({ row }: { row: { original: StreetRow } }) => row.original.town?.name ?? "—",
    },
  ],
  toForm: (row) => ({ name: row.name, town_id: String(row.town_id) }),
  fromForm: (form) => ({ ...form, town_id: Number(form.town_id) }),
};

export default function StreetsPage() {
  return <SimpleCRUDPage config={config} />;
}
