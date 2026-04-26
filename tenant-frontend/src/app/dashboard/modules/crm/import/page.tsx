"use client";

import { useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listCrmImportJobs,
  createCrmImportJob,
  deleteCrmImportJob,
  downloadCrmImportTemplate,
} from "@/lib/tenant-resources";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { FileSpreadsheet } from "lucide-react";
import { toast } from "sonner";

type ImportJob = {
  id: number;
  entity_type: string;
  file_name: string;
  status: string;
  total_rows: number;
  processed_rows: number;
  failed_rows: number;
  created_at?: string;
};

const STATUS_COLORS: Record<string, string> = {
  pending: "bg-yellow-100 text-yellow-800",
  processing: "bg-blue-100 text-blue-800",
  completed: "bg-green-100 text-green-800",
  failed: "bg-red-100 text-red-800",
};

const config: SimpleCRUDConfig<ImportJob> = {
  titleKey: "dashboard.crm.import_jobs",
  titleFallback: "Import Jobs",
  subtitleKey: "dashboard.crm.import_jobs_subtitle",
  subtitleFallback: "Import leads, contacts, and companies from CSV/Excel",
  createLabelKey: "dashboard.crm.new_import",
  createLabelFallback: "New Import",
  fields: [
    {
      name: "entity_type",
      label: "Import Type",
      type: "select",
      required: true,
      options: [
        { value: "leads", label: "Leads" },
        { value: "contacts", label: "Contacts" },
        { value: "companies", label: "Companies" },
        { value: "opportunities", label: "Opportunities" },
      ],
    },
    { name: "file_path", label: "File Path", type: "text", placeholder: "Path to CSV/Excel file", required: true },
    {
      name: "skip_header",
      label: "Skip Header Row",
      type: "select",
      options: [
        { value: "true", label: "Yes" },
        { value: "false", label: "No" },
      ],
    },
  ],
  listFn: listCrmImportJobs as () => Promise<ImportJob[]>,
  createFn: createCrmImportJob,
  updateFn: async () => { throw new Error("Cannot update import jobs"); },
  deleteFn: deleteCrmImportJob as unknown as (id: number) => Promise<void>,
  moduleKey: "crm",
  dashboardHref: "/dashboard/modules/crm",
  columns: (t): Array<ColumnDef<ImportJob>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "entity_type", header: t("dashboard.crm.import_type", "Type"), cell: ({ row }) => <span className="capitalize">{row.original.entity_type}</span> },
    { accessorKey: "file_name", header: t("dashboard.crm.file_name", "File") },
    {
      accessorKey: "status",
      header: t("dashboard.table.status", "Status"),
      cell: ({ row }) => (
        <Badge className={STATUS_COLORS[row.original.status] || "bg-gray-100"}>
          {row.original.status}
        </Badge>
      ),
    },
    {
      accessorKey: "progress",
      header: t("dashboard.crm.progress", "Progress"),
      cell: ({ row }) => {
        const { processed_rows, total_rows, failed_rows } = row.original;
        return (
          <span className="text-sm">
            {processed_rows}/{total_rows}
            {failed_rows > 0 && <span className="text-red-500 ml-1">({failed_rows} failed)</span>}
          </span>
        );
      },
    },
    { accessorKey: "created_at", header: t("dashboard.table.date", "Date") },
  ],
  toForm: (r) => ({
    entity_type: r.entity_type ?? "leads",
    file_path: "",
    skip_header: "true",
  }),
  fromForm: (f) => ({
    entity_type: f.entity_type,
    file_path: f.file_path,
    skip_header: f.skip_header === "true",
  }),
};

export default function CrmImportPage() {
  const [downloadingTemplate, setDownloadingTemplate] = useState<string | null>(null);

  const handleDownloadTemplate = async (entityType: string) => {
    setDownloadingTemplate(entityType);
    try {
      await downloadCrmImportTemplate(entityType);
      toast.success(`${entityType} template downloaded`);
    } catch {
      toast.error("Failed to download template");
    } finally {
      setDownloadingTemplate(null);
    }
  };

  return (
    <div className="p-6 space-y-6">
      <div className="flex gap-2">
        <div className="text-sm text-muted-foreground mr-2">Download templates:</div>
        {["leads", "contacts", "companies", "opportunities"].map((type) => (
          <Button
            key={type}
            variant="outline"
            size="sm"
            className="gap-2"
            disabled={downloadingTemplate === type}
            onClick={() => handleDownloadTemplate(type)}
          >
            <FileSpreadsheet className="w-4 h-4" />
            {type.charAt(0).toUpperCase() + type.slice(1)}
          </Button>
        ))}
      </div>
      <SimpleCRUDPage config={config} />
    </div>
  );
}
