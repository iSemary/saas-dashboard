"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listReleases, createRelease, deleteRelease, type ReleaseRow } from "@/lib/resources";

const config: SimpleCRUDConfig<ReleaseRow> = {
  titleKey: "dashboard.releases.title",
  titleFallback: "Releases",
  subtitleKey: "dashboard.releases.subtitle",
  subtitleFallback: "Manage platform release notes.",
  createLabelKey: "dashboard.releases.create",
  createLabelFallback: "Add Release",
  fields: [
    { name: "version", label: "Version", required: true },
    { name: "title", label: "Title", required: true },
    { name: "body", label: "Body", type: "richtext" },
    { name: "release_date", label: "Release Date", type: "datetime" },
    { name: "is_published", label: "Published", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listReleases,
  createFn: createRelease,
  deleteFn: deleteRelease,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "version", header: t("dashboard.releases.version", "Version") },
    { accessorKey: "title", header: t("dashboard.releases.title_col", "Title") },
  ],
  toForm: (row) => ({ version: row.version, title: row.title, body: row.body ?? "", is_published: row.is_published ? "1" : "0" }),
  fromForm: (form) => ({ ...form, is_published: form.is_published === "1" }),
};

export default function ReleasesPage() {
  return <SimpleCRUDPage config={config} />;
}
