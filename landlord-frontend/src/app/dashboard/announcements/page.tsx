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
    { name: "title", label: "Title", required: true },
    { name: "body", label: "Body", type: "textarea" },
    { name: "type", label: "Type", type: "select", options: [{ value: "info", label: "Info" }, { value: "warning", label: "Warning" }, { value: "success", label: "Success" }, { value: "error", label: "Error" }] },
    { name: "is_active", label: "Active", type: "select", options: [{ value: "1", label: "Yes" }, { value: "0", label: "No" }] },
  ],
  listFn: listAnnouncements,
  createFn: createAnnouncement,
  updateFn: updateAnnouncement,
  deleteFn: deleteAnnouncement,
  columns: (t) => [
    { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
    { accessorKey: "title", header: t("dashboard.announcements.title_col", "Title") },
    { accessorKey: "type", header: t("dashboard.announcements.type", "Type") },
  ],
  toForm: (row) => ({ title: row.title, body: row.body ?? "", type: row.type, is_active: row.is_active ? "1" : "0" }),
  fromForm: (form) => ({ ...form, is_active: form.is_active === "1" }),
};

export default function AnnouncementsPage() {
  return <SimpleCRUDPage config={config} />;
}
