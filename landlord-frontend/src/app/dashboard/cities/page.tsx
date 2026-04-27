"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { Upload } from "lucide-react";
import { listCities, createCity, updateCity, deleteCity, listProvinces, type CityRow } from "@/lib/resources";

const config: SimpleCRUDConfig<CityRow> = {
  titleKey: "dashboard.cities.title",
  titleFallback: "Cities",
  subtitleKey: "dashboard.cities.subtitle",
  subtitleFallback: "Manage cities within provinces.",
  createLabelKey: "dashboard.cities.create",
  createLabelFallback: "Add City",
  actionButtons: [
    {
      labelKey: "dashboard.import",
      labelFallback: "Import",
      icon: Upload,
      variant: "outline",
      href: "/dashboard/import/cities",
    },
  ],
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "postal_code", label: "Postal Code", placeholder: "12345" },
    { name: "is_capital", label: "Capital", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
    { name: "phone_code", label: "Phone Code", placeholder: "+1" },
    { name: "timezone", label: "Timezone", placeholder: "UTC" },
    { name: "province_id", label: "Province", type: "entity", listFn: listProvinces, optionLabelKey: "name", optionValueKey: "id", parentKey: "country", required: true },
    { name: "latitude", label: "Latitude", type: "number", placeholder: "0" },
    { name: "longitude", label: "Longitude", type: "number", placeholder: "0" },
    { name: "area_km2", label: "Area (km²)", type: "number", placeholder: "0" },
    { name: "population", label: "Population", type: "number", placeholder: "0" },
    { name: "elevation_m", label: "Elevation (m)", type: "number", placeholder: "0" },
  ],
  listFn: listCities,
  createFn: createCity,
  updateFn: updateCity,
  deleteFn: deleteCity,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    {
      id: "province",
      header: t("dashboard.cities.province", "Province"),
      cell: ({ row }: { row: { original: CityRow } }) => {
        const province = row.original.province;
        if (typeof province === "string" && province.trim().length > 0) return province;
        if (province && typeof province === "object" && "name" in province) return province.name;
        return row.original.province_name ?? "—";
      },
    },
  ],
  toForm: (row) => ({ name: row.name, postal_code: row.postal_code ?? "", is_capital: row.is_capital ? "1" : "0", phone_code: row.phone_code ?? "", timezone: row.timezone ?? "", province_id: String(row.province_id), latitude: row.latitude ? String(row.latitude) : "", longitude: row.longitude ? String(row.longitude) : "", area_km2: row.area_km2 ? String(row.area_km2) : "", population: row.population ? String(row.population) : "", elevation_m: row.elevation_m ? String(row.elevation_m) : "" }),
  fromForm: (form) => ({ ...form, province_id: Number(form.province_id), is_capital: form.is_capital === "1", latitude: form.latitude ? Number(form.latitude) : null, longitude: form.longitude ? Number(form.longitude) : null, area_km2: form.area_km2 ? Number(form.area_km2) : null, population: form.population ? Number(form.population) : null, elevation_m: form.elevation_m ? Number(form.elevation_m) : null }),
};

export default function CitiesPage() {
  return <SimpleCRUDPage config={config} />;
}
