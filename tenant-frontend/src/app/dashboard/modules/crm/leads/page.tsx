"use client";

import { ColumnDef } from "@tanstack/react-table";
import { Badge } from "@/components/ui/badge";
import { SimpleCRUDPage } from "@/components/simple-crud-page";
import {
  listCrmLeads,
  createCrmLead,
  updateCrmLead,
  deleteCrmLead,
} from "@/lib/tenant-resources";

interface Lead {
  id: number;
  name: string;
  email: string | null;
  phone: string | null;
  company: string | null;
  status: string;
  source: string | null;
  expected_revenue: number | null;
  assigned_to: number | null;
}

const STATUS_COLORS: Record<string, string> = {
  new: "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300",
  contacted: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300",
  qualified: "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300",
  unqualified: "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300",
  converted: "bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300",
};

const columns = (): Array<ColumnDef<Lead>> => [
  { accessorKey: "name", header: "Name" },
  { accessorKey: "email", header: "Email", cell: ({ row }) => row.original.email ?? "—" },
  { accessorKey: "company", header: "Company", cell: ({ row }) => row.original.company ?? "—" },
  {
    accessorKey: "status",
    header: "Status",
    cell: ({ row }) => (
      <Badge className={STATUS_COLORS[row.original.status] ?? ""}>{row.original.status}</Badge>
    ),
  },
  { accessorKey: "source", header: "Source", cell: ({ row }) => row.original.source ?? "—" },
];

export default function LeadsPage() {
  return (
    <SimpleCRUDPage<Lead>
      config={{
        titleKey: "dashboard.crm.leads",
        titleFallback: "Leads",
        subtitleKey: "dashboard.crm.leads_subtitle",
        subtitleFallback: "Manage your sales leads and prospects",
        createLabelKey: "dashboard.crm.add_lead",
        createLabelFallback: "Add Lead",
        moduleKey: "crm",
        dashboardHref: "/dashboard/modules/crm",
        serverSide: true,
        fields: [
          { name: "name", label: "Name", required: true },
          { name: "email", label: "Email", type: "email" },
          { name: "phone", label: "Phone" },
          { name: "company", label: "Company" },
          { name: "title", label: "Title" },
          {
            name: "status",
            label: "Status",
            type: "select",
            options: [
              { value: "new", label: "New" },
              { value: "contacted", label: "Contacted" },
              { value: "qualified", label: "Qualified" },
              { value: "unqualified", label: "Unqualified" },
            ],
          },
          {
            name: "source",
            label: "Source",
            type: "select",
            options: [
              { value: "website", label: "Website" },
              { value: "referral", label: "Referral" },
              { value: "social_media", label: "Social Media" },
              { value: "cold_call", label: "Cold Call" },
              { value: "email", label: "Email" },
              { value: "other", label: "Other" },
            ],
          },
          { name: "expected_revenue", label: "Expected Revenue", type: "number" },
          { name: "description", label: "Description", type: "textarea" },
        ],
        columns,
        listFn: listCrmLeads,
        createFn: createCrmLead,
        updateFn: updateCrmLead,
        deleteFn: deleteCrmLead,
        toForm: (row) => ({
          name: row.name ?? "",
          email: row.email ?? "",
          phone: row.phone ?? "",
          company: row.company ?? "",
          status: row.status ?? "new",
          source: row.source ?? "",
          expected_revenue: row.expected_revenue != null ? String(row.expected_revenue) : "",
        }),
        fromForm: (form) => ({
          name: form.name,
          email: form.email || undefined,
          phone: form.phone || undefined,
          company: form.company || undefined,
          status: form.status || "new",
          source: form.source || undefined,
          expected_revenue: form.expected_revenue ? parseFloat(form.expected_revenue) : undefined,
        }),
      }}
    />
  );
}
