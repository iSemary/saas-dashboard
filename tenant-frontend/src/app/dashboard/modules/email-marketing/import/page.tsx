"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmImportJobs, createEmImportJob, deleteEmImportJob, type EmImportJob } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmImportJob>[] => [
  { accessorKey: "contact_list_id", header: t("email_marketing.contact_list_id", "Contact List ID") },
  { accessorKey: "file_path", header: t("email_marketing.file_path", "File") },
  { accessorKey: "status", header: t("email_marketing.status", "Status") },
  { accessorKey: "total_rows", header: t("email_marketing.total_rows", "Total Rows") },
  { accessorKey: "processed_rows", header: t("email_marketing.processed_rows", "Processed") },
  { accessorKey: "failed_rows", header: t("email_marketing.failed_rows", "Failed") },
  { accessorKey: "created_at", header: t("email_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "contact_list_id", label: "Contact List ID", type: "number", required: true },
  { name: "file_path", label: "File Path", required: true },
];

export default function EmImportJobsPage() {
  return (
    <SimpleCRUDPage<EmImportJob>
      config={{
        titleKey: "email_marketing.import_jobs",
        titleFallback: "Import Jobs",
        subtitleKey: "email_marketing.import_jobs_subtitle",
        subtitleFallback: "Manage contact imports",
        createLabelKey: "email_marketing.add_import",
        createLabelFallback: "New Import",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmImportJobs(params),
        createFn: createEmImportJob,
        updateFn: async () => {},
        deleteFn: deleteEmImportJob,
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
