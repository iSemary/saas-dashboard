"use client";
import { ColumnDef } from "@tanstack/react-table";
import { SimpleCRUDPage, FieldDef } from "@/components/simple-crud-page";
import type { TableParams } from "@/lib/tenant-resources";
import { listTmCalendarEvents, createTmCalendarEvent, updateTmCalendarEvent, deleteTmCalendarEvent } from "@/lib/tm-resources";

type CalendarEvent = { id: number; title: string; start_time: string; end_time: string; type: string; location: string; is_all_day: boolean };

const columns = (t: (k: string, f: string) => string): ColumnDef<CalendarEvent>[] => [
  { accessorKey: "title", header: t("tm.title", "Title"), meta: { searchable: true } },
  { accessorKey: "start_time", header: t("tm.start", "Start") },
  { accessorKey: "end_time", header: t("tm.end", "End") },
  { accessorKey: "type", header: t("tm.type", "Type") },
  { accessorKey: "location", header: t("tm.location", "Location") },
  { accessorKey: "is_all_day", header: t("tm.all_day", "All Day"), cell: ({ row }) => row.original.is_all_day ? "Yes" : "No" },
];

const fields: FieldDef[] = [
  { name: "title", label: "Title", required: true },
  { name: "description", label: "Description", type: "textarea" },
  { name: "start_time", label: "Start Time", required: true },
  { name: "end_time", label: "End Time", required: true },
  { name: "type", label: "Type", type: "select", options: [
    { value: "meeting", label: "Meeting" }, { value: "reminder", label: "Reminder" },
    { value: "task", label: "Task" }, { value: "event", label: "Event" },
  ]},
  { name: "location", label: "Location" },
  { name: "is_all_day", label: "All Day", type: "select", options: [
    { value: "1", label: "Yes" }, { value: "0", label: "No" },
  ]},
];

export default function CalendarPage() {
  return (
    <SimpleCRUDPage<CalendarEvent>
      config={{
        titleKey: "tm.calendar",
        titleFallback: "Calendar",
        subtitleKey: "tm.calendar_subtitle",
        subtitleFallback: "View and manage your calendar events",
        createLabelKey: "tm.add_event",
        createLabelFallback: "Add Event",
        moduleKey: "time_management",
        dashboardHref: "/dashboard/modules/time-management",
        serverSide: true,
        fields,
        columns,
        listFn: (params?: TableParams) => listTmCalendarEvents<CalendarEvent>(params),
        createFn: createTmCalendarEvent,
        updateFn: (id: number, p: Record<string, unknown>) => updateTmCalendarEvent(id, p),
        deleteFn: deleteTmCalendarEvent,
        toForm: (row) => ({
          title: row.title ?? "", start_time: row.start_time ?? "", end_time: row.end_time ?? "",
          type: row.type ?? "event", location: row.location ?? "", is_all_day: row.is_all_day ? "1" : "0",
        }),
        fromForm: (form) => ({
          title: form.title, start_time: form.start_time, end_time: form.end_time,
          type: form.type || "event", location: form.location || undefined,
          is_all_day: form.is_all_day === "1",
        }),
      }}
    />
  );
}
