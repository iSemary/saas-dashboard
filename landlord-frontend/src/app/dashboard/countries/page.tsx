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
    { name: "phone_code", label: "Phone Code", placeholder: "+1" },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
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
  toForm: (row) => ({ name: row.name, code: row.code, phone_code: row.phone_code ?? "", is_active: row.is_active ? "1" : "0" }),
  fromForm: (form) => ({ ...form, is_active: form.is_active === "1" }),
};

export default function CountriesPage() {
  return <SimpleCRUDPage config={config} />;
}
