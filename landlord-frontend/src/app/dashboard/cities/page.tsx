"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listCities, createCity, deleteCity, listProvinces, type CityRow } from "@/lib/resources";

const config: SimpleCRUDConfig<CityRow> = {
  titleKey: "dashboard.cities.title",
  titleFallback: "Cities",
  subtitleKey: "dashboard.cities.subtitle",
  subtitleFallback: "Manage cities within provinces.",
  createLabelKey: "dashboard.cities.create",
  createLabelFallback: "Add City",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "province_id", label: "Province", type: "entity", listFn: listProvinces, optionLabelKey: "name", optionValueKey: "id", parentKey: "country", required: true },
  ],
  listFn: listCities,
  createFn: createCity,
  deleteFn: deleteCity,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    {
      id: "province",
      header: t("dashboard.cities.province", "Province"),
      cell: ({ row }: { row: { original: CityRow } }) => row.original.province?.name ?? "—",
    },
  ],
  toForm: (row) => ({ name: row.name, province_id: String(row.province_id) }),
  fromForm: (form) => ({ ...form, province_id: Number(form.province_id) }),
};

export default function CitiesPage() {
  return <SimpleCRUDPage config={config} />;
}
