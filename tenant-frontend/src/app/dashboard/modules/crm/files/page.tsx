"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listCrmFiles,
  createCrmFile,
  deleteCrmFile,
} from "@/lib/tenant-resources";

type CrmFile = {
  id: number;
  name: string;
  file_type?: string;
  size_bytes?: number;
  related_type?: string;
  related_id?: number;
  created_by?: { name: string };
  created_at?: string;
};

const formatFileSize = (bytes: number | undefined) => {
  if (!bytes) return "—";
  const units = ["B", "KB", "MB", "GB"];
  let size = bytes;
  let unitIndex = 0;
  while (size >= 1024 && unitIndex < units.length - 1) {
    size /= 1024;
    unitIndex++;
  }
  return `${size.toFixed(1)} ${units[unitIndex]}`;
};

const config: SimpleCRUDConfig<CrmFile> = {
  titleKey: "dashboard.crm.files",
  titleFallback: "CRM Files",
  subtitleKey: "dashboard.crm.files_subtitle",
  subtitleFallback: "Manage files attached to CRM entities",
  createLabelKey: "dashboard.crm.upload_file",
  createLabelFallback: "Upload File",
  fields: [
    { name: "name", label: "File Name", placeholder: "File name", required: true },
    { name: "file_path", label: "File", type: "text", placeholder: "File path or upload", required: true },
    {
      name: "related_type",
      label: "Related To",
      type: "select",
      options: [
        { value: "", label: "-- None --" },
        { value: "lead", label: "Lead" },
        { value: "contact", label: "Contact" },
        { value: "company", label: "Company" },
        { value: "opportunity", label: "Opportunity" },
      ],
    },
    { name: "related_id", label: "Related ID", type: "number", placeholder: "e.g., 1" },
  ],
  listFn: listCrmFiles as () => Promise<CrmFile[]>,
  createFn: createCrmFile,
  updateFn: async () => { throw new Error("Update not supported for files"); },
  deleteFn: deleteCrmFile as unknown as (id: number) => Promise<void>,
  moduleKey: "crm",
  dashboardHref: "/dashboard/modules/crm",
  columns: (t): Array<ColumnDef<CrmFile>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "name", header: t("dashboard.crm.file_name", "Name") },
    { accessorKey: "file_type", header: t("dashboard.crm.file_type", "Type") },
    {
      accessorKey: "size_bytes",
      header: t("dashboard.crm.file_size", "Size"),
      cell: ({ row }) => formatFileSize(row.original.size_bytes),
    },
    {
      accessorKey: "related_type",
      header: t("dashboard.crm.related_to", "Related To"),
      cell: ({ row }) =>
        row.original.related_type
          ? `${row.original.related_type} #${row.original.related_id}`
          : "—",
    },
  ],
  toForm: (r) => ({
    name: r.name ?? "",
    file_path: "",
    related_type: r.related_type ?? "",
    related_id: r.related_id ? String(r.related_id) : "",
  }),
  fromForm: (f) => ({
    name: f.name,
    file_path: f.file_path,
    related_type: f.related_type || undefined,
    related_id: f.related_id ? Number(f.related_id) : undefined,
  }),
};

export default function CrmFilesPage() {
  return <SimpleCRUDPage config={config} />;
}
