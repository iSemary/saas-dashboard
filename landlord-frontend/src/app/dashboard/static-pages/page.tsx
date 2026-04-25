"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listStaticPages, createStaticPage, updateStaticPage, deleteStaticPage, type StaticPageRow } from "@/lib/resources";

const config: SimpleCRUDConfig<StaticPageRow> = {
  titleKey: "dashboard.static_pages.title",
  titleFallback: "Static Pages",
  subtitleKey: "dashboard.static_pages.subtitle",
  subtitleFallback: "Manage static content pages.",
  createLabelKey: "dashboard.static_pages.create",
  createLabelFallback: "Add Page",
  fields: [
    { name: "title", label: "Title", required: true },
    { name: "slug", label: "Slug", type: "slug", sourceField: "title" },
    { name: "body", label: "Body", type: "richtext" },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listStaticPages,
  createFn: createStaticPage,
  updateFn: updateStaticPage,
  deleteFn: deleteStaticPage,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "title", header: t("dashboard.static_pages.title_col", "Title") },
    { accessorKey: "slug", header: t("dashboard.static_pages.slug", "Slug") },
  ],
  toForm: (row) => ({ title: row.title, slug: row.slug, description: row.description ?? "", body: row.body ?? "", image: row.image ?? "", status: row.status ?? "active" }),
  fromForm: (form) => form,
};

export default function StaticPagesPage() {
  return <SimpleCRUDPage config={config} />;
}
