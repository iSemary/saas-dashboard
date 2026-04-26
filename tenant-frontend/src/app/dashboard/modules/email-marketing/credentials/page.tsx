"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmCredentials, createEmCredential, updateEmCredential, deleteEmCredential, type EmCredential } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmCredential>[] => [
  { accessorKey: "name", header: t("email_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "provider", header: t("email_marketing.provider", "Provider") },
  { accessorKey: "from_email", header: t("email_marketing.from_email", "From Email") },
  { accessorKey: "is_default", header: t("email_marketing.is_default", "Default") },
  { accessorKey: "status", header: t("email_marketing.status", "Status") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "provider", label: "Provider", type: "select", required: true, options: [
    { value: "smtp", label: "SMTP" }, { value: "ses", label: "Amazon SES" },
    { value: "mailgun", label: "Mailgun" }, { value: "sendgrid", label: "SendGrid" },
  ]},
  { name: "host", label: "Host" },
  { name: "port", label: "Port", type: "number" },
  { name: "username", label: "Username" },
  { name: "password", label: "Password", type: "password" },
  { name: "from_email", label: "From Email" },
  { name: "from_name", label: "From Name" },
  { name: "is_default", label: "Default", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "status", label: "Status", type: "select", options: [
    { value: "active", label: "Active" }, { value: "inactive", label: "Inactive" },
  ]},
];

export default function EmCredentialsPage() {
  return (
    <SimpleCRUDPage<EmCredential>
      config={{
        titleKey: "email_marketing.credentials",
        titleFallback: "Credentials",
        subtitleKey: "email_marketing.credentials_subtitle",
        subtitleFallback: "Manage email provider credentials",
        createLabelKey: "email_marketing.add_credential",
        createLabelFallback: "Add Credential",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmCredentials(params),
        createFn: createEmCredential,
        updateFn: updateEmCredential,
        deleteFn: deleteEmCredential,
        toForm: (row) => ({
          name: row.name ?? "", provider: row.provider ?? "smtp", host: row.host ?? "",
          port: row.port?.toString() ?? "", username: row.username ?? "", password: "",
          from_email: row.from_email ?? "", from_name: row.from_name ?? "",
          is_default: row.is_default ? "1" : "0", status: row.status ?? "active",
        }),
        fromForm: (form) => ({
          name: form.name, provider: form.provider, host: form.host || undefined,
          port: form.port ? Number(form.port) : undefined, username: form.username || undefined,
          password: form.password || undefined, from_email: form.from_email || undefined,
          from_name: form.from_name || undefined, is_default: form.is_default === "1",
          status: form.status || "active",
        }),
      }}
    />
  );
}
