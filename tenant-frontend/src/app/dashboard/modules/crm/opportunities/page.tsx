"use client";

import { ColumnDef } from "@tanstack/react-table";
import { Badge } from "@/components/ui/badge";
import { SimpleCRUDPage } from "@/components/simple-crud-page";
import {
  listCrmOpportunities,
  createCrmOpportunity,
  updateCrmOpportunity,
  deleteCrmOpportunity,
} from "@/lib/tenant-resources";

interface Opportunity {
  id: number;
  name: string;
  stage: string;
  expected_revenue: number | null;
  probability: number | null;
  expected_close_date: string | null;
  description: string | null;
  assigned_to: number | null;
}

const STAGE_COLORS: Record<string, string> = {
  prospecting: "bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200",
  qualification: "bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300",
  proposal: "bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300",
  negotiation: "bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300",
  closed_won: "bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300",
  closed_lost: "bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300",
};

const columns = (): Array<ColumnDef<Opportunity>> => [
  { accessorKey: "name", header: "Name" },
  {
    accessorKey: "stage",
    header: "Stage",
    cell: ({ row }) => (
      <Badge className={STAGE_COLORS[row.original.stage] ?? ""}>{row.original.stage.replace("_", " ")}</Badge>
    ),
  },
  {
    accessorKey: "expected_revenue",
    header: "Revenue",
    cell: ({ row }) => row.original.expected_revenue != null ? `$${Number(row.original.expected_revenue).toLocaleString()}` : "—",
  },
  {
    accessorKey: "probability",
    header: "Probability",
    cell: ({ row }) => row.original.probability != null ? `${row.original.probability}%` : "—",
  },
  {
    accessorKey: "expected_close_date",
    header: "Close Date",
    cell: ({ row }) => row.original.expected_close_date ?? "—",
  },
];

export default function OpportunitiesPage() {
  return (
    <SimpleCRUDPage<Opportunity>
      config={{
        titleKey: "dashboard.crm.opportunities",
        titleFallback: "Opportunities",
        subtitleKey: "dashboard.crm.opportunities_subtitle",
        subtitleFallback: "Track and manage your sales pipeline",
        createLabelKey: "dashboard.crm.add_opportunity",
        createLabelFallback: "Add Opportunity",
        moduleKey: "crm",
        dashboardHref: "/dashboard/modules/crm",
        serverSide: true,
        fields: [
          { name: "name", label: "Name", required: true },
          {
            name: "stage",
            label: "Stage",
            type: "select",
            options: [
              { value: "prospecting", label: "Prospecting" },
              { value: "qualification", label: "Qualification" },
              { value: "proposal", label: "Proposal" },
              { value: "negotiation", label: "Negotiation" },
              { value: "closed_won", label: "Closed Won" },
              { value: "closed_lost", label: "Closed Lost" },
            ],
          },
          { name: "expected_revenue", label: "Expected Revenue", type: "number" },
          { name: "probability", label: "Probability (%)", type: "number" },
          { name: "expected_close_date", label: "Expected Close Date", type: "text", placeholder: "YYYY-MM-DD" },
          { name: "description", label: "Description", type: "textarea" },
        ],
        columns,
        listFn: listCrmOpportunities,
        createFn: createCrmOpportunity,
        updateFn: updateCrmOpportunity,
        deleteFn: deleteCrmOpportunity,
        toForm: (row) => ({
          name: row.name ?? "",
          stage: row.stage ?? "prospecting",
          expected_revenue: row.expected_revenue != null ? String(row.expected_revenue) : "",
          probability: row.probability != null ? String(row.probability) : "",
          expected_close_date: row.expected_close_date ?? "",
          description: row.description ?? "",
        }),
        fromForm: (form) => ({
          name: form.name,
          stage: form.stage || "prospecting",
          expected_revenue: form.expected_revenue ? parseFloat(form.expected_revenue) : undefined,
          probability: form.probability ? parseFloat(form.probability) : undefined,
          expected_close_date: form.expected_close_date || undefined,
          description: form.description || undefined,
        }),
      }}
    />
  );
}
