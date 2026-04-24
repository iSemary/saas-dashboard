"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listTranslations, createTranslation, updateTranslation, deleteTranslation, type TranslationRow } from "@/lib/resources";

const config: SimpleCRUDConfig<TranslationRow> = {
  titleKey: "dashboard.translations.title",
  titleFallback: "Translations",
  subtitleKey: "dashboard.translations.subtitle",
  subtitleFallback: "Manage translation key-value pairs.",
  createLabelKey: "dashboard.translations.create",
  createLabelFallback: "Add Translation",
  fields: [
    { name: "key", label: "Key", required: true },
    { name: "value", label: "Value", type: "textarea" },
    { name: "group", label: "Group", placeholder: "dashboard" },
    { name: "language_id", label: "Language ID", type: "number", required: true },
  ],
  listFn: listTranslations,
  createFn: createTranslation,
  updateFn: updateTranslation,
  deleteFn: deleteTranslation,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "key", header: t("dashboard.translations.key", "Key") },
    { accessorKey: "value", header: t("dashboard.translations.value", "Value") },
    {
      id: "language",
      header: t("dashboard.translations.language", "Language"),
      cell: ({ row }: { row: { original: TranslationRow } }) => row.original.language?.name ?? "—",
    },
  ],
  toForm: (row) => ({ key: row.key, value: row.value, group: row.group ?? "", language_id: String(row.language_id) }),
  fromForm: (form) => ({ ...form, language_id: Number(form.language_id) }),
};

export default function TranslationsPage() {
  return <SimpleCRUDPage config={config} />;
}
