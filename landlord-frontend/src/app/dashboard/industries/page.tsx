"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listIndustries, createIndustry, deleteIndustry, type IndustryRow } from "@/lib/resources";

const config: SimpleCRUDConfig<IndustryRow> = {
  titleKey: "dashboard.industries.title",
  titleFallback: "Industries",
  subtitleKey: "dashboard.industries.subtitle",
  subtitleFallback: "Manage industry classifications.",
  createLabelKey: "dashboard.industries.create",
  createLabelFallback: "Add Industry",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "name" },
  ],
  listFn: listIndustries,
  createFn: createIndustry,
  deleteFn: deleteIndustry,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.industries.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug }),
  fromForm: (form) => form,
};

export default function IndustriesPage() {
  return <SimpleCRUDPage config={config} />;
}
