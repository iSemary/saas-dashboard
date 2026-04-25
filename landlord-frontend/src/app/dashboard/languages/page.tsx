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
    { name: "code", label: "Code", required: true },
    { name: "locale", label: "Locale", placeholder: "en", required: true },
    { name: "direction", label: "Direction", type: "select", options: [{ value: "ltr", label: "LTR" }, { value: "rtl", label: "RTL" }] },
  ],
  listFn: listLanguages,
  createFn: createLanguage,
  updateFn: updateLanguage,
  deleteFn: deleteLanguage,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
    { accessorKey: "locale", header: t("dashboard.languages.code", "Locale") },
    { accessorKey: "direction", header: t("dashboard.languages.direction", "Direction") },
  ],
  toForm: (row) => ({ name: row.name, code: row.code, locale: row.locale, direction: row.direction }),
  fromForm: (form) => form,
};

export default function LanguagesPage() {
  return <SimpleCRUDPage config={config} />;
}
