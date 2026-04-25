"use client";

import { SimpleCRUDPage, type SimpleCRUDConfig } from "@/components/simple-crud-page";
import { listAnnouncements, createAnnouncement, updateAnnouncement, deleteAnnouncement, type AnnouncementRow } from "@/lib/resources";

const config: SimpleCRUDConfig<AnnouncementRow> = {
  titleKey: "dashboard.announcements.title",
  titleFallback: "Announcements",
  subtitleKey: "dashboard.announcements.subtitle",
  subtitleFallback: "Manage platform announcements.",
  createLabelKey: "dashboard.announcements.create",
  createLabelFallback: "Add Announcement",
  fields: [
    { name: "name", label: "Name", required: true },
    { name: "description", label: "Description", type: "textarea" },
    { name: "body", label: "Body", type: "textarea" },
    { name: "type", label: "Type", type: "select", options: [{ value: "info", label: "Info" }, { value: "warning", label: "Warning" }, { value: "success", label: "Success" }, { value: "error", label: "Error" }] },
    { name: "start_at", label: "Start Date", type: "datetime" },
    { name: "end_at", label: "End Date", type: "datetime" },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listAnnouncements,
  createFn: createAnnouncement,
  updateFn: updateAnnouncement,
  deleteFn: deleteAnnouncement,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "name", header: t("dashboard.announcements.title_col", "Name") },
    { accessorKey: "type", header: t("dashboard.announcements.type", "Type") },
  ],
  toForm: (row) => ({ name: row.name ?? "", description: row.description ?? "", body: row.body ?? "", type: row.type ?? "info", start_at: row.start_at ?? "", end_at: row.end_at ?? "", is_active: row.is_active ? "1" : "0" }),
  fromForm: (form) => ({ ...form, is_active: form.is_active === "1" }),
};

export default function AnnouncementsPage() {
  return <SimpleCRUDPage config={config} />;
}
