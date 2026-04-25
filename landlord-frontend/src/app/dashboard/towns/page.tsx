"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listTowns, createTown, deleteTown, listCities, type TownRow } from "@/lib/resources";

const config: SimpleCRUDConfig<TownRow> = {
  titleKey: "dashboard.towns.title",
  titleFallback: "Towns",
  subtitleKey: "dashboard.towns.subtitle",
  subtitleFallback: "Manage towns within cities.",
  createLabelKey: "dashboard.towns.create",
  createLabelFallback: "Add Town",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "city_id", label: "City", type: "entity", listFn: listCities, optionLabelKey: "name", optionValueKey: "id", parentKey: "province", required: true },
    { name: "latitude", label: "Latitude", type: "number", placeholder: "0" },
    { name: "longitude", label: "Longitude", type: "number", placeholder: "0" },
    { name: "area_km2", label: "Area (km²)", type: "number", placeholder: "0" },
    { name: "population", label: "Population", type: "number", placeholder: "0" },
    { name: "elevation_m", label: "Elevation (m)", type: "number", placeholder: "0" },
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
  toForm: (row) => ({ name: row.name, city_id: String(row.city_id), latitude: row.latitude ? String(row.latitude) : "", longitude: row.longitude ? String(row.longitude) : "", area_km2: row.area_km2 ? String(row.area_km2) : "", population: row.population ? String(row.population) : "", elevation_m: row.elevation_m ? String(row.elevation_m) : "" }),
  fromForm: (form) => ({ ...form, city_id: Number(form.city_id), latitude: form.latitude ? Number(form.latitude) : null, longitude: form.longitude ? Number(form.longitude) : null, area_km2: form.area_km2 ? Number(form.area_km2) : null, population: form.population ? Number(form.population) : null, elevation_m: form.elevation_m ? Number(form.elevation_m) : null }),
};

export default function TownsPage() {
  return <SimpleCRUDPage config={config} />;
}
