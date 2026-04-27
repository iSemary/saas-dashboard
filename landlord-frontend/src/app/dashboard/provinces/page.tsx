"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { Upload } from "lucide-react";
import { listProvinces, createProvince, updateProvince, deleteProvince, listCountries, type ProvinceRow } from "@/lib/resources";

const config: SimpleCRUDConfig<ProvinceRow> = {
  titleKey: "dashboard.provinces.title",
  titleFallback: "Provinces",
  subtitleKey: "dashboard.provinces.subtitle",
  subtitleFallback: "Manage provinces/states within countries.",
  createLabelKey: "dashboard.provinces.create",
  createLabelFallback: "Add Province",
  actionButtons: [
    {
      labelKey: "dashboard.import",
      labelFallback: "Import",
      icon: Upload,
      variant: "outline",
      href: "/dashboard/import/provinces",
    },
  ],
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "code", label: "Code" },
    { name: "country_id", label: "Country", type: "entity", listFn: listCountries, optionLabelKey: "name", optionValueKey: "id", required: true },
  ],
  listFn: listProvinces,
  createFn: createProvince,
  updateFn: updateProvince,
  deleteFn: deleteProvince,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "code", header: t("dashboard.provinces.code", "Code") },
    {
      id: "country",
      header: t("dashboard.provinces.country", "Country"),
      cell: ({ row }: { row: { original: ProvinceRow } }) => row.original.country?.name ?? "—",
    },
  ],
  toForm: (row) => ({ name: row.name, code: row.code ?? "", country_id: String(row.country_id), flag: row.flag ?? "", latitude: row.latitude ? String(row.latitude) : "", longitude: row.longitude ? String(row.longitude) : "", area_km2: row.area_km2 ? String(row.area_km2) : "", population: row.population ? String(row.population) : "" }),
  fromForm: (form) => ({ ...form, country_id: Number(form.country_id), latitude: form.latitude ? Number(form.latitude) : null, longitude: form.longitude ? Number(form.longitude) : null, area_km2: form.area_km2 ? Number(form.area_km2) : null, population: form.population ? Number(form.population) : null }),
};

export default function ProvincesPage() {
  return <SimpleCRUDPage config={config} />;
}
