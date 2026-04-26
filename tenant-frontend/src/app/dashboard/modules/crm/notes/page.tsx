"use client";

import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, SimpleCRUDConfig } from "@/components/simple-crud-page";
import {
  listCrmNotes,
  createCrmNote,
  updateCrmNote,
  deleteCrmNote,
} from "@/lib/tenant-resources";

type CrmNote = {
  id: number;
  title: string;
  content?: string;
  related_type?: string;
  related_id?: number;
  created_by?: { name: string };
  created_at?: string;
};

const config: SimpleCRUDConfig<CrmNote> = {
  titleKey: "dashboard.crm.notes",
  titleFallback: "CRM Notes",
  subtitleKey: "dashboard.crm.notes_subtitle",
  subtitleFallback: "Manage notes for leads, contacts, companies, and opportunities",
  createLabelKey: "dashboard.crm.add_note",
  createLabelFallback: "Add Note",
  fields: [
    { name: "title", label: "Title", placeholder: "Note title", required: true },
    { name: "content", label: "Content", type: "textarea", placeholder: "Note content..." },
    {
      name: "related_type",
      label: "Related To",
      type: "select",
      options: [
        { value: "", label: "-- None --" },
        { value: "lead", label: "Lead" },
        { value: "contact", label: "Contact" },
        { value: "company", label: "Company" },
        { value: "opportunity", label: "Opportunity" },
      ],
    },
    { name: "related_id", label: "Related ID", type: "number", placeholder: "e.g., 1" },
  ],
  listFn: listCrmNotes as () => Promise<CrmNote[]>,
  createFn: createCrmNote,
  updateFn: updateCrmNote,
  deleteFn: deleteCrmNote as unknown as (id: number) => Promise<void>,
  moduleKey: "crm",
  dashboardHref: "/dashboard/modules/crm",
  columns: (t): Array<ColumnDef<CrmNote>> => [
    { accessorKey: "id", header: t("dashboard.table.id", "ID") },
    { accessorKey: "title", header: t("dashboard.crm.note_title", "Title") },
    {
      accessorKey: "related_type",
      header: t("dashboard.crm.related_to", "Related To"),
      cell: ({ row }) =>
        row.original.related_type
          ? `${row.original.related_type} #${row.original.related_id}`
          : "—",
    },
    {
      accessorKey: "created_by",
      header: t("dashboard.crm.created_by", "Created By"),
      cell: ({ row }) => row.original.created_by?.name ?? "—",
    },
    { accessorKey: "created_at", header: t("dashboard.table.date", "Date") },
  ],
  toForm: (r) => ({
    title: r.title ?? "",
    content: r.content ?? "",
    related_type: r.related_type ?? "",
    related_id: r.related_id ? String(r.related_id) : "",
  }),
  fromForm: (f) => ({
    title: f.title,
    content: f.content || undefined,
    related_type: f.related_type || undefined,
    related_id: f.related_id ? Number(f.related_id) : undefined,
  }),
};

export default function CrmNotesPage() {
  return <SimpleCRUDPage config={config} />;
}
