"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listLanguages, createLanguage, updateLanguage, deleteLanguage, type LanguageRow } from "@/lib/resources";

const config: SimpleCRUDConfig<LanguageRow> = {
  titleKey: "dashboard.languages.title",
  titleFallback: "Languages",
  subtitleKey: "dashboard.languages.subtitle",
  subtitleFallback: "Manage supported languages and their settings.",
  createLabelKey: "dashboard.languages.create",
  createLabelFallback: "Add Language",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "code", label: "Code", placeholder: "en", required: true },
    { name: "direction", label: "Direction", type: "select", options: [{ value: "ltr", label: "LTR" }, { value: "rtl", label: "RTL" }] },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
    { name: "is_default", label: "Default", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listLanguages,
  createFn: createLanguage,
  updateFn: updateLanguage,
  deleteFn: deleteLanguage,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "code", header: t("dashboard.languages.code", "Code") },
    { accessorKey: "direction", header: t("dashboard.languages.direction", "Direction") },
  ],
  toForm: (row) => ({ name: row.name, code: row.code, direction: row.direction, is_active: row.is_active ? "1" : "0", is_default: row.is_default ? "1" : "0" }),
  fromForm: (form) => ({ ...form, is_active: form.is_active === "1", is_default: form.is_default === "1" }),
};

export default function LanguagesPage() {
  return <SimpleCRUDPage config={config} />;
}
