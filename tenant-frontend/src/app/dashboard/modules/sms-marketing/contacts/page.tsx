"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmContacts, createSmContact, updateSmContact, deleteSmContact, type SmContact } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmContact>[] => [
  { accessorKey: "phone", header: t("sms_marketing.phone", "Phone"), meta: { searchable: true } },
  { accessorKey: "first_name", header: t("sms_marketing.first_name", "First Name") },
  { accessorKey: "last_name", header: t("sms_marketing.last_name", "Last Name") },
  { accessorKey: "email", header: t("sms_marketing.email", "Email") },
  { accessorKey: "status", header: t("sms_marketing.status", "Status") },
];

const fields: FieldDef[] = [
  { name: "phone", label: "Phone", required: true },
  { name: "first_name", label: "First Name" },
  { name: "last_name", label: "Last Name" },
  { name: "email", label: "Email" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "active", label: "Active" }, { value: "opted_out", label: "Opted Out" }, { value: "invalid", label: "Invalid" },
  ]},
];

export default function SmContactsPage() {
  return (
    <SimpleCRUDPage<SmContact>
      config={{
        titleKey: "sms_marketing.contacts",
        titleFallback: "Contacts",
        subtitleKey: "sms_marketing.contacts_subtitle",
        subtitleFallback: "Manage SMS contacts",
        createLabelKey: "sms_marketing.add_contact",
        createLabelFallback: "Add Contact",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmContacts(params),
        createFn: createSmContact,
        updateFn: updateSmContact,
        deleteFn: deleteSmContact,
        toForm: (row) => ({
          phone: row.phone ?? "", first_name: row.first_name ?? "", last_name: row.last_name ?? "",
          email: row.email ?? "", status: row.status ?? "active",
        }),
        fromForm: (form) => ({
          phone: form.phone, first_name: form.first_name || undefined,
          last_name: form.last_name || undefined, email: form.email || undefined,
          status: form.status || "active",
        }),
      }}
    />
  );
}
