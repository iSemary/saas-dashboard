"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmCredentials, createSmCredential, updateSmCredential, deleteSmCredential, type SmCredential } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmCredential>[] => [
  { accessorKey: "name", header: t("sms_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "provider", header: t("sms_marketing.provider", "Provider") },
  { accessorKey: "from_number", header: t("sms_marketing.from_number", "From Number") },
  { accessorKey: "is_default", header: t("sms_marketing.is_default", "Default") },
  { accessorKey: "status", header: t("sms_marketing.status", "Status") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "provider", label: "Provider", type: "select", required: true, options: [
    { value: "twilio", label: "Twilio" }, { value: "vonage", label: "Vonage" },
    { value: "messagebird", label: "MessageBird" }, { value: "mock", label: "Mock (Log)" },
  ]},
  { name: "account_sid", label: "Account SID" },
  { name: "auth_token", label: "Auth Token", type: "password" },
  { name: "from_number", label: "From Number" },
  { name: "is_default", label: "Default", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
  { name: "status", label: "Status", type: "select", options: [
    { value: "active", label: "Active" }, { value: "inactive", label: "Inactive" },
  ]},
];

export default function SmCredentialsPage() {
  return (
    <SimpleCRUDPage<SmCredential>
      config={{
        titleKey: "sms_marketing.credentials",
        titleFallback: "Credentials",
        subtitleKey: "sms_marketing.credentials_subtitle",
        subtitleFallback: "Manage SMS provider credentials",
        createLabelKey: "sms_marketing.add_credential",
        createLabelFallback: "Add Credential",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmCredentials(params),
        createFn: createSmCredential,
        updateFn: updateSmCredential,
        deleteFn: deleteSmCredential,
        toForm: (row) => ({
          name: row.name ?? "", provider: row.provider ?? "mock", account_sid: row.account_sid ?? "",
          auth_token: "", from_number: row.from_number ?? "",
          is_default: row.is_default ? "1" : "0", status: row.status ?? "active",
        }),
        fromForm: (form) => ({
          name: form.name, provider: form.provider, account_sid: form.account_sid || undefined,
          auth_token: form.auth_token || undefined, from_number: form.from_number || undefined,
          is_default: form.is_default === "1", status: form.status || "active",
        }),
      }}
    />
  );
}
