"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage } from "@/components/simple-crud-page";
import {
  listCrmCompanies,
  createCrmCompany,
  updateCrmCompany,
  deleteCrmCompany,
} from "@/lib/tenant-resources";

interface Company {
  id: number;
  name: string;
  email: string | null;
  phone: string | null;
  website: string | null;
  industry: string | null;
  type: string | null;
}

const columns = (): Array<ColumnDef<Company>> => [
  { accessorKey: "name", header: "Name" },
  { accessorKey: "email", header: "Email", cell: ({ row }) => row.original.email ?? "—" },
  { accessorKey: "phone", header: "Phone", cell: ({ row }) => row.original.phone ?? "—" },
  { accessorKey: "industry", header: "Industry", cell: ({ row }) => row.original.industry ?? "—" },
  { accessorKey: "type", header: "Type", cell: ({ row }) => row.original.type ?? "—" },
  {
    accessorKey: "website",
    header: "Website",
    cell: ({ row }) =>
      row.original.website ? (
        <a href={row.original.website} target="_blank" rel="noopener noreferrer" className="text-primary underline-offset-4 hover:underline">
          {row.original.website}
        </a>
      ) : "—",
  },
];

export default function CrmCompaniesPage() {
  return (
    <SimpleCRUDPage<Company>
      config={{
        titleKey: "dashboard.crm.companies",
        titleFallback: "Companies",
        subtitleKey: "dashboard.crm.companies_subtitle",
        subtitleFallback: "Manage your CRM companies",
        createLabelKey: "dashboard.crm.add_company",
        createLabelFallback: "Add Company",
        moduleKey: "crm",
        dashboardHref: "/dashboard/modules/crm",
        serverSide: true,
        fields: [
          { name: "name", label: "Name", required: true },
          { name: "email", label: "Email", type: "email" },
          { name: "phone", label: "Phone" },
          { name: "website", label: "Website", type: "url" },
          {
            name: "industry",
            label: "Industry",
            type: "select",
            options: [
              { value: "technology", label: "Technology" },
              { value: "finance", label: "Finance" },
              { value: "healthcare", label: "Healthcare" },
              { value: "retail", label: "Retail" },
              { value: "manufacturing", label: "Manufacturing" },
              { value: "education", label: "Education" },
              { value: "real_estate", label: "Real Estate" },
              { value: "other", label: "Other" },
            ],
          },
          {
            name: "type",
            label: "Type",
            type: "select",
            options: [
              { value: "prospect", label: "Prospect" },
              { value: "customer", label: "Customer" },
              { value: "partner", label: "Partner" },
              { value: "vendor", label: "Vendor" },
            ],
          },
          { name: "description", label: "Description", type: "textarea" },
        ],
        columns,
        listFn: listCrmCompanies,
        createFn: createCrmCompany,
        updateFn: updateCrmCompany,
        deleteFn: deleteCrmCompany,
        toForm: (row) => ({
          name: row.name ?? "",
          email: row.email ?? "",
          phone: row.phone ?? "",
          website: row.website ?? "",
          industry: row.industry ?? "",
          type: row.type ?? "",
        }),
        fromForm: (form) => ({
          name: form.name,
          email: form.email || undefined,
          phone: form.phone || undefined,
          website: form.website || undefined,
          industry: form.industry || undefined,
          type: form.type || undefined,
        }),
      }}
    />
  );
}
