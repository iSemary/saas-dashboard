"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmImportJobs, createSmImportJob, deleteSmImportJob, type SmImportJob } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmImportJob>[] => [
  { accessorKey: "contact_list_id", header: t("sms_marketing.contact_list_id", "Contact List ID") },
  { accessorKey: "file_path", header: t("sms_marketing.file_path", "File") },
  { accessorKey: "status", header: t("sms_marketing.status", "Status") },
  { accessorKey: "total_rows", header: t("sms_marketing.total_rows", "Total Rows") },
  { accessorKey: "processed_rows", header: t("sms_marketing.processed_rows", "Processed") },
  { accessorKey: "failed_rows", header: t("sms_marketing.failed_rows", "Failed") },
  { accessorKey: "created_at", header: t("sms_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "contact_list_id", label: "Contact List ID", type: "number", required: true },
  { name: "file_path", label: "File Path", required: true },
];

export default function SmImportJobsPage() {
  return (
    <SimpleCRUDPage<SmImportJob>
      config={{
        titleKey: "sms_marketing.import_jobs",
        titleFallback: "Import Jobs",
        subtitleKey: "sms_marketing.import_jobs_subtitle",
        subtitleFallback: "Manage contact imports",
        createLabelKey: "sms_marketing.add_import",
        createLabelFallback: "New Import",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmImportJobs(params),
        createFn: createSmImportJob,
        updateFn: async () => {},
        deleteFn: deleteSmImportJob,
        toForm: (row) => ({
          contact_list_id: row.contact_list_id?.toString() ?? "", file_path: row.file_path ?? "",
        }),
        fromForm: (form) => ({
          contact_list_id: Number(form.contact_list_id), file_path: form.file_path,
        }),
      }}
    />
  );
}
