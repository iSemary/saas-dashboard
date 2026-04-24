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
    { name: "slug", label: "Slug" },
  ],
  listFn: listUnits,
  createFn: createUnit,
  deleteFn: deleteUnit,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.units.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug }),
  fromForm: (form) => form,
};

export default function UnitsPage() {
  return <SimpleCRUDPage config={config} />;
}
