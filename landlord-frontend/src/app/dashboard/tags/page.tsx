"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listTags, createTag, deleteTag, type TagRow } from "@/lib/resources";

const config: SimpleCRUDConfig<TagRow> = {
  titleKey: "dashboard.tags.title",
  titleFallback: "Tags",
  subtitleKey: "dashboard.tags.subtitle",
  subtitleFallback: "Manage tags for content classification.",
  createLabelKey: "dashboard.tags.create",
  createLabelFallback: "Add Tag",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "slug", label: "Slug" },
  ],
  listFn: listTags,
  createFn: createTag,
  deleteFn: deleteTag,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "slug", header: t("dashboard.tags.slug", "Slug") },
  ],
  toForm: (row) => ({ name: row.name, slug: row.slug }),
  fromForm: (form) => form,
};

export default function TagsPage() {
  return <SimpleCRUDPage config={config} />;
}
