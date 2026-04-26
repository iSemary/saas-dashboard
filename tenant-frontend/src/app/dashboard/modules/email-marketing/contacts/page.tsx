"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmContacts, createEmContact, updateEmContact, deleteEmContact, type EmContact } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmContact>[] => [
  { accessorKey: "email", header: t("email_marketing.email", "Email"), meta: { searchable: true } },
  { accessorKey: "first_name", header: t("email_marketing.first_name", "First Name") },
  { accessorKey: "last_name", header: t("email_marketing.last_name", "Last Name") },
  { accessorKey: "status", header: t("email_marketing.status", "Status") },
  { accessorKey: "created_at", header: t("email_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "email", label: "Email", required: true },
  { name: "first_name", label: "First Name" },
  { name: "last_name", label: "Last Name" },
  { name: "phone", label: "Phone" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "active", label: "Active" }, { value: "unsubscribed", label: "Unsubscribed" }, { value: "bounced", label: "Bounced" },
  ]},
];

export default function EmContactsPage() {
  return (
    <SimpleCRUDPage<EmContact>
      config={{
        titleKey: "email_marketing.contacts",
        titleFallback: "Contacts",
        subtitleKey: "email_marketing.contacts_subtitle",
        subtitleFallback: "Manage email contacts",
        createLabelKey: "email_marketing.add_contact",
        createLabelFallback: "Add Contact",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmContacts(params),
        createFn: createEmContact,
        updateFn: updateEmContact,
        deleteFn: deleteEmContact,
        toForm: (row) => ({
          email: row.email ?? "", first_name: row.first_name ?? "", last_name: row.last_name ?? "",
          phone: row.phone ?? "", status: row.status ?? "active",
        }),
        fromForm: (form) => ({
          email: form.email, first_name: form.first_name || undefined,
          last_name: form.last_name || undefined, phone: form.phone || undefined,
          status: form.status || "active",
        }),
      }}
    />
  );
}
