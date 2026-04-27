"use client";

import { ColumnDef } from "@tanstack/react-table";
import { useRouter } from "next/navigation";
import { LayoutGrid } from "lucide-react";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listTickets, createTicket, updateTicket, deleteTicket } from "@/lib/tenant-resources";
import { Badge } from "@/components/ui/badge";

type Ticket = { id: number; subject: string; status: string; priority: string; assigned_to?: number; created_at?: string };

const config: SimpleCRUDConfig<Ticket> = {
  titleKey: "dashboard.tickets.title",
  titleFallback: "Tickets",
  subtitleKey: "dashboard.tickets.subtitle",
  subtitleFallback: "Manage support tickets",
  createLabelKey: "dashboard.tickets.create",
  createLabelFallback: "New Ticket",
  actionButtons: [
    {
      labelKey: "dashboard.tickets.kanban",
      labelFallback: "Kanban Board",
      icon: LayoutGrid,
      variant: "outline",
      href: "/dashboard/tickets/kanban",
    },
  ],
  fields: [
    { name: "subject", label: "Subject", placeholder: "Issue with...", required: true },
    { name: "body", label: "Description", type: "richtext", placeholder: "Describe the issue...", required: true },
    { name: "priority", label: "Priority", type: "select", options: [{ value: "low", label: "Low" }, { value: "medium", label: "Medium" }, { value: "high", label: "High" }, { value: "urgent", label: "Urgent" }] },
    { name: "status", label: "Status", type: "select", options: [{ value: "open", label: "Open" }, { value: "in_progress", label: "In Progress" }, { value: "closed", label: "Closed" }] },
  ],
  listFn: listTickets as () => Promise<Ticket[]>,
  createFn: createTicket,
  updateFn: updateTicket,
  deleteFn: deleteTicket as unknown as (id: number) => Promise<void>,
  columns: (t): Array<ColumnDef<Ticket>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "subject", header: t("dashboard.table.subject", "Subject") },
    { accessorKey: "status", header: t("dashboard.table.status", "Status"), cell: ({ row }) => <Badge variant={row.original.status === "closed" ? "secondary" : row.original.status === "in_progress" ? "default" : "outline"}>{row.original.status}</Badge> },
    { accessorKey: "priority", header: t("dashboard.table.priority", "Priority"), cell: ({ row }) => <Badge variant={row.original.priority === "urgent" ? "destructive" : row.original.priority === "high" ? "default" : "outline"}>{row.original.priority}</Badge> },
  ],
  toForm: (r) => ({ subject: r.subject, body: "", priority: r.priority ?? "low", status: r.status ?? "open" }),
  fromForm: (f) => ({ subject: f.subject, body: f.body, priority: f.priority, status: f.status }),
};

export default function TicketsPage() {
  const router = useRouter();

  // Add row click handler to config
  const configWithNavigation = {
    ...config,
    columns: (t: (key: string, fallback: string) => string): Array<ColumnDef<Ticket>> => [
      {
        accessorKey: "id",
        header: t("dashboard.table.id", "ID"),
        cell: ({ row }) => (
          <span
            className="cursor-pointer text-primary hover:underline"
            onClick={() => router.push(`/dashboard/tickets/${row.original.id}`)}
          >
            #{row.original.id}
          </span>
        ),
      },
      {
        accessorKey: "subject",
        header: t("dashboard.table.subject", "Subject"),
        cell: ({ row }) => (
          <span
            className="cursor-pointer hover:text-primary"
            onClick={() => router.push(`/dashboard/tickets/${row.original.id}`)}
          >
            {row.original.subject}
          </span>
        ),
      },
      {
        accessorKey: "status",
        header: t("dashboard.table.status", "Status"),
        cell: ({ row }) => (
          <Badge
            variant={
              row.original.status === "closed"
                ? "secondary"
                : row.original.status === "in_progress"
                  ? "default"
                  : "outline"
            }
          >
            {row.original.status}
          </Badge>
        ),
      },
      {
        accessorKey: "priority",
        header: t("dashboard.table.priority", "Priority"),
        cell: ({ row }) => (
          <Badge
            variant={
              row.original.priority === "urgent"
                ? "destructive"
                : row.original.priority === "high"
                  ? "default"
                  : "outline"
            }
          >
            {row.original.priority}
          </Badge>
        ),
      },
    ],
  };

  return <SimpleCRUDPage config={configWithNavigation} />;
}
