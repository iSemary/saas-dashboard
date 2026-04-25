"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listCountries, createCountry, updateCountry, deleteCountry, type CountryRow } from "@/lib/resources";

const config: SimpleCRUDConfig<CountryRow> = {
  titleKey: "dashboard.countries.title",
  titleFallback: "Countries",
  subtitleKey: "dashboard.countries.subtitle",
  subtitleFallback: "Manage countries for geography setup.",
  createLabelKey: "dashboard.countries.create",
  createLabelFallback: "Add Country",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "code", label: "Code", placeholder: "US", required: true },
    { name: "region", label: "Region", placeholder: "Americas" },
    { name: "flag", label: "Flag", type: "file", accept: "image/*" },
    { name: "phone_code", label: "Phone Code", placeholder: "+1" },
    { name: "timezone", label: "Timezone", placeholder: "UTC" },
    { name: "latitude", label: "Latitude", type: "number", placeholder: "0" },
    { name: "longitude", label: "Longitude", type: "number", placeholder: "0" },
    { name: "currency_code", label: "Currency Code", placeholder: "USD" },
    { name: "currency_symbol", label: "Currency Symbol", placeholder: "$" },
    { name: "language_code", label: "Language Code", placeholder: "en" },
    { name: "area_km2", label: "Area (km²)", type: "number", placeholder: "0" },
    { name: "population", label: "Population", type: "number", placeholder: "0" },
  ],
  listFn: listCountries,
  createFn: createCountry,
  updateFn: updateCountry,
  deleteFn: deleteCountry,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "code", header: t("dashboard.countries.code", "Code") },
    { accessorKey: "phone_code", header: t("dashboard.countries.phone_code", "Phone Code") },
  ],
  toForm: (row) => ({ name: row.name, code: row.code, region: row.region ?? "", flag: row.flag ?? "", phone_code: row.phone_code ?? "", timezone: row.timezone ?? "", latitude: row.latitude ? String(row.latitude) : "", longitude: row.longitude ? String(row.longitude) : "", currency_code: row.currency_code ?? "", currency_symbol: row.currency_symbol ?? "", language_code: row.language_code ?? "", area_km2: row.area_km2 ? String(row.area_km2) : "", population: row.population ? String(row.population) : "" }),
  fromForm: (form) => ({ ...form, latitude: form.latitude ? Number(form.latitude) : null, longitude: form.longitude ? Number(form.longitude) : null, area_km2: form.area_km2 ? Number(form.area_km2) : null, population: form.population ? Number(form.population) : null }),
};

export default function CountriesPage() {
  return <SimpleCRUDPage config={config} />;
}
