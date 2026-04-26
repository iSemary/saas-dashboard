"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getEmContactLists, createEmContactList, updateEmContactList, deleteEmContactList, type EmContactList } from "@/lib/api-email-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<EmContactList>[] => [
  { accessorKey: "name", header: t("email_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "description", header: t("email_marketing.description", "Description") },
  { accessorKey: "contacts_count", header: t("email_marketing.contacts_count", "Contacts") },
  { accessorKey: "status", header: t("email_marketing.status", "Status") },
  { accessorKey: "created_at", header: t("email_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "active", label: "Active" }, { value: "archived", label: "Archived" },
  ]},
];

export default function EmContactListsPage() {
  return (
    <SimpleCRUDPage<EmContactList>
      config={{
        titleKey: "email_marketing.contact_lists",
        titleFallback: "Contact Lists",
        subtitleKey: "email_marketing.contact_lists_subtitle",
        subtitleFallback: "Manage contact lists",
        createLabelKey: "email_marketing.add_contact_list",
        createLabelFallback: "Add Contact List",
        moduleKey: "email_marketing",
        dashboardHref: "/dashboard/modules/email-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getEmContactLists(params),
        createFn: createEmContactList,
        updateFn: updateEmContactList,
        deleteFn: deleteEmContactList,
        toForm: (row) => ({
          name: row.name ?? "", description: row.description ?? "", status: row.status ?? "active",
        }),
        fromForm: (form) => ({
          name: form.name, description: form.description || undefined, status: form.status || "active",
        }),
      }}
    />
  );
}
