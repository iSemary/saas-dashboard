"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage } from "@/components/simple-crud-page";
import {
  listCrmContacts,
  createCrmContact,
  updateCrmContact,
  deleteCrmContact,
} from "@/lib/tenant-resources";

interface Contact {
  id: number;
  first_name: string;
  last_name: string;
  email: string | null;
  phone: string | null;
  title: string | null;
  company_id: number | null;
}

const columns = (): Array<ColumnDef<Contact>> => [
  {
    id: "name",
    header: "Name",
    cell: ({ row }) => `${row.original.first_name} ${row.original.last_name}`,
  },
  { accessorKey: "email", header: "Email", cell: ({ row }) => row.original.email ?? "—" },
  { accessorKey: "phone", header: "Phone", cell: ({ row }) => row.original.phone ?? "—" },
  { accessorKey: "title", header: "Title", cell: ({ row }) => row.original.title ?? "—" },
];

export default function CrmContactsPage() {
  return (
    <SimpleCRUDPage<Contact>
      config={{
        titleKey: "dashboard.crm.contacts",
        titleFallback: "Contacts",
        subtitleKey: "dashboard.crm.contacts_subtitle",
        subtitleFallback: "Manage your CRM contacts",
        createLabelKey: "dashboard.crm.add_contact",
        createLabelFallback: "Add Contact",
        moduleKey: "crm",
        dashboardHref: "/dashboard/modules/crm",
        serverSide: true,
        fields: [
          { name: "first_name", label: "First Name", required: true },
          { name: "last_name", label: "Last Name", required: true },
          { name: "email", label: "Email", type: "email" },
          { name: "phone", label: "Phone" },
          { name: "title", label: "Title" },
          { name: "description", label: "Description", type: "textarea" },
        ],
        columns,
        listFn: listCrmContacts,
        createFn: createCrmContact,
        updateFn: updateCrmContact,
        deleteFn: deleteCrmContact,
        toForm: (row) => ({
          first_name: row.first_name ?? "",
          last_name: row.last_name ?? "",
          email: row.email ?? "",
          phone: row.phone ?? "",
          title: row.title ?? "",
        }),
        fromForm: (form) => ({
          first_name: form.first_name,
          last_name: form.last_name,
          email: form.email || undefined,
          phone: form.phone || undefined,
          title: form.title || undefined,
        }),
      }}
    />
  );
}
