"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig, type ActionButton } from "@/components/simple-crud-page";
import { File, RefreshCw, Search, FileText, Loader2 } from "lucide-react";
import { toast } from "sonner";
import { useState } from "react";
import {
  listTranslations,
  createTranslation,
  updateTranslation,
  deleteTranslation,
  listLanguages,
  generateTranslationsJson,
  syncMissingTranslations,
  syncJsonFiles,
  scanTranslationJs,
  scanTranslationPhp,
  type TranslationRow,
} from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

const config: SimpleCRUDConfig<TranslationRow> = {
  titleKey: "dashboard.translations.title",
  titleFallback: "Translations",
  subtitleKey: "dashboard.translations.subtitle",
  subtitleFallback: "Manage translation key-value pairs.",
  createLabelKey: "dashboard.translations.create",
  createLabelFallback: "Add Translation",
  fields: [
    { name: "translation_key", label: "Key", required: true },
    { name: "translation_value", label: "Value", type: "textarea", required: true },
    { name: "translation_context", label: "Context", type: "textarea" },
    { name: "is_shareable", label: "Shareable", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
    { name: "language_id", label: "Language", type: "entity", listFn: listLanguages, optionLabelKey: "name", optionValueKey: "id", required: true },
  ],
  listFn: listTranslations,
  createFn: createTranslation,
  updateFn: updateTranslation,
  deleteFn: deleteTranslation,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "translation_key", header: t("dashboard.translations.key", "Key") },
    { accessorKey: "translation_value", header: t("dashboard.translations.value", "Value") },
    {
      id: "language",
      header: t("dashboard.translations.language", "Language"),
      cell: ({ row }: { row: { original: TranslationRow } }) => row.original.language?.name ?? "—",
    },
  ],
  toForm: (row) => ({ translation_key: row.translation_key, translation_value: row.translation_value, translation_context: row.translation_context ?? "", is_shareable: row.is_shareable ? "1" : "0", language_id: String(row.language_id) }),
  fromForm: (form) => ({ ...form, language_id: Number(form.language_id), is_shareable: form.is_shareable === "1" }),
};

export default function TranslationsPage() {
  const { t } = useI18n();
  const [generatingJson, setGeneratingJson] = useState(false);
  const [syncingMissing, setSyncingMissing] = useState(false);
  const [syncingJson, setSyncingJson] = useState(false);
  const [scanningJs, setScanningJs] = useState(false);
  const [scanningPhp, setScanningPhp] = useState(false);

  const actionButtons: ActionButton[] = [
    {
      labelKey: "dashboard.translations.generate_json",
      labelFallback: generatingJson ? "Generating..." : "Generate JSON",
      icon: generatingJson ? Loader2 : File,
      variant: "outline",
      className: "text-orange-600 border-orange-600 hover:bg-orange-50",
      disabled: generatingJson,
      ...(generatingJson && { iconClassName: "animate-spin" }),
      onClick: async () => {
        setGeneratingJson(true);
        try {
          await generateTranslationsJson();
          toast.success(t("dashboard.translations.json_generated", "JSON files generated successfully"));
        } catch {
          toast.error(t("dashboard.translations.json_generate_failed", "Failed to generate JSON files"));
        } finally {
          setGeneratingJson(false);
        }
      },
    },
    {
      labelKey: "dashboard.translations.sync_missing",
      labelFallback: syncingMissing ? "Processing..." : "Sync Missing",
      icon: syncingMissing ? Loader2 : RefreshCw,
      variant: "outline",
      className: "text-orange-600 border-orange-600 hover:bg-orange-50",
      disabled: syncingMissing,
      ...(syncingMissing && { iconClassName: "animate-spin" }),
      onClick: async () => {
        setSyncingMissing(true);
        try {
          await syncMissingTranslations();
          toast.success(t("dashboard.translations.missing_synced", "Missing translations synced successfully"));
        } catch {
          toast.error(t("dashboard.translations.sync_failed", "Failed to sync missing translations"));
        } finally {
          setSyncingMissing(false);
        }
      },
    },
    {
      labelKey: "dashboard.translations.sync_json",
      labelFallback: syncingJson ? "Syncing..." : "Sync JSON Files",
      icon: syncingJson ? Loader2 : RefreshCw,
      variant: "outline",
      disabled: syncingJson,
      ...(syncingJson && { iconClassName: "animate-spin" }),
      onClick: async () => {
        setSyncingJson(true);
        try {
          await syncJsonFiles();
          toast.success(t("dashboard.translations.json_synced", "JSON files synced successfully"));
        } catch {
          toast.error(t("dashboard.translations.json_sync_failed", "Failed to sync JSON files"));
        } finally {
          setSyncingJson(false);
        }
      },
    },
    {
      labelKey: "dashboard.translations.scan_js",
      labelFallback: scanningJs ? "Scanning..." : "Scan JS Files",
      icon: scanningJs ? Loader2 : Search,
      variant: "outline",
      disabled: scanningJs,
      ...(scanningJs && { iconClassName: "animate-spin" }),
      onClick: async () => {
        setScanningJs(true);
        try {
          const result = await scanTranslationJs();
          console.log("JS scan result:", result);
          toast.success(t("dashboard.translations.js_scanned", "JS files scanned successfully"));
        } catch {
          toast.error(t("dashboard.translations.js_scan_failed", "Failed to scan JS files"));
        } finally {
          setScanningJs(false);
        }
      },
    },
    {
      labelKey: "dashboard.translations.scan_php",
      labelFallback: scanningPhp ? "Scanning..." : "Scan PHP Files",
      icon: scanningPhp ? Loader2 : Search,
      variant: "outline",
      disabled: scanningPhp,
      ...(scanningPhp && { iconClassName: "animate-spin" }),
      onClick: async () => {
        setScanningPhp(true);
        try {
          const result = await scanTranslationPhp();
          console.log("PHP scan result:", result);
          toast.success(t("dashboard.translations.php_scanned", "PHP files scanned successfully"));
        } catch {
          toast.error(t("dashboard.translations.php_scan_failed", "Failed to scan PHP files"));
        } finally {
          setScanningPhp(false);
        }
      },
    },
    {
      labelKey: "dashboard.translations.status",
      labelFallback: "Translation Status",
      icon: FileText,
      variant: "outline",
      href: "/dashboard/translations/status",
    },
  ];

  return <SimpleCRUDPage config={{ ...config, actionButtons }} />;
}
