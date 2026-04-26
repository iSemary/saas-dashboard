"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { getSmContactLists, createSmContactList, updateSmContactList, deleteSmContactList, type SmContactList } from "@/lib/api-sms-marketing";

const columns = (t: (k: string, f: string) => string): ColumnDef<SmContactList>[] => [
  { accessorKey: "name", header: t("sms_marketing.name", "Name"), meta: { searchable: true } },
  { accessorKey: "description", header: t("sms_marketing.description", "Description") },
  { accessorKey: "contacts_count", header: t("sms_marketing.contacts_count", "Contacts") },
  { accessorKey: "status", header: t("sms_marketing.status", "Status") },
  { accessorKey: "created_at", header: t("sms_marketing.created_at", "Created At") },
];

const fields: FieldDef[] = [
  { name: "name", label: "Name", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "status", label: "Status", type: "select", options: [
    { value: "active", label: "Active" }, { value: "archived", label: "Archived" },
  ]},
];

export default function SmContactListsPage() {
  return (
    <SimpleCRUDPage<SmContactList>
      config={{
        titleKey: "sms_marketing.contact_lists",
        titleFallback: "Contact Lists",
        subtitleKey: "sms_marketing.contact_lists_subtitle",
        subtitleFallback: "Manage contact lists",
        createLabelKey: "sms_marketing.add_contact_list",
        createLabelFallback: "Add Contact List",
        moduleKey: "sms_marketing",
        dashboardHref: "/dashboard/modules/sms-marketing",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => getSmContactLists(params),
        createFn: createSmContactList,
        updateFn: updateSmContactList,
        deleteFn: deleteSmContactList,
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
