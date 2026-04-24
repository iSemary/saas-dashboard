"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listTypes, createType, deleteType, type TypeRow } from "@/lib/resources";

const config: SimpleCRUDConfig<TypeRow> = {
  titleKey: "dashboard.types.title",
  titleFallback: "Types",
  subtitleKey: "dashboard.types.subtitle",
  subtitleFallback: "Manage content types.",
  createLabelKey: "dashboard.types.create",
  createLabelFallback: "Add Type",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug" },
  ],
  listFn: listTypes,
  createFn: createType,
  deleteFn: deleteType,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.types.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug }),
  fromForm: (form) => form,
};

export default function TypesPage() {
  return <SimpleCRUDPage config={config} />;
}
