"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listUnits, createUnit, deleteUnit, type UnitRow } from "@/lib/resources";

const config: SimpleCRUDConfig<UnitRow> = {
  titleKey: "dashboard.units.title",
  titleFallback: "Units",
  subtitleKey: "dashboard.units.subtitle",
  subtitleFallback: "Manage measurement units.",
  createLabelKey: "dashboard.units.create",
  createLabelFallback: "Add Unit",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "code", label: "Code", placeholder: "e.g., kg, g, lb", required: true },
    { name: "type_id", label: "Type", placeholder: "e.g., weight, length, volume", required: true },
    { name: "base_conversion", label: "Base Conversion", type: "number", placeholder: "Conversion factor to base unit" },
    { name: "description", label: "Description", type: "textarea" },
    { name: "is_base_unit", label: "Is Base Unit", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listUnits,
  createFn: createUnit,
  deleteFn: deleteUnit,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "code", header: t("dashboard.units.code", "Code") },
    { accessorKey: "type_id", header: t("dashboard.units.type", "Type") },
    { accessorKey: "is_base_unit", header: t("dashboard.units.base_unit", "Base Unit") },
  ],
  toForm: (row) => ({ name: row.name, code: row.code, type_id: row.type_id, base_conversion: row.base_conversion ? String(row.base_conversion) : "", description: row.description ?? "", is_base_unit: row.is_base_unit ? "1" : "0" }),
  fromForm: (form) => ({ ...form, base_conversion: form.base_conversion ? Number(form.base_conversion) : null, is_base_unit: form.is_base_unit === "1" }),
};

export default function UnitsPage() {
  return <SimpleCRUDPage config={config} />;
}
