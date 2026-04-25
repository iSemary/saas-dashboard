"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Badge } from "@/components/ui/badge";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";

type TicketRow = {
  id: number;
  ticket_number: string;
  title: string;
  description: string | null;
  status: string;
  priority: string;
  assigned_to: number | null;
  brand_id: number | null;
  due_date: string | null;
  created_at?: string;
};

export default function TicketsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<TicketRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ ticket_number: "", title: "", description: "", status: "open", priority: "medium", assigned_to: "", brand_id: "", due_date: "" });

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get("/tickets");
      setRows(Array.isArray(res.data) ? (res.data as TicketRow[]) : []);
    } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setForm({ ticket_number: "", title: "", description: "", status: "open", priority: "medium", assigned_to: "", brand_id: "", due_date: "" });
    setSheetOpen(true);
  };

  const openEdit = (row: TicketRow) => {
    setEditingId(row.id);
    setForm({ ticket_number: row.ticket_number, title: row.title, description: row.description ?? "", status: row.status, priority: row.priority, assigned_to: row.assigned_to ? String(row.assigned_to) : "", brand_id: row.brand_id ? String(row.brand_id) : "", due_date: row.due_date ?? "" });
    setSheetOpen(true);
  };

  const save = async () => {
    setSaving(true);
    try {
      if (editingId == null) {
        await api.post("/tickets", form);
        toast.success(t("dashboard.tickets.toast_created", "Ticket created."));
      } else {
        await api.put(`/tickets/${editingId}`, form);
        toast.success(t("dashboard.tickets.toast_updated", "Ticket updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.tickets.toast_save_error", "Save failed."));
    } finally {
      setSaving(false);
    }
  };

  const remove = async (id: number) => {
    setDeletingId(id);
    setConfirmOpen(true);
  };

  const handleConfirmDelete = async () => {
    if (deletingId === null) return;
    try {
      await api.delete(`/tickets/${deletingId}`);
      toast.success(t("dashboard.tickets.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.tickets.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<TicketRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "ticket_number", header: t("dashboard.tickets.ticket_number", "Ticket #") },
      { accessorKey: "title", header: t("dashboard.tickets.title_col", "Title") },
      {
        accessorKey: "status",
        header: t("dashboard.tickets.status", "Status"),
        cell: ({ row }) => (
          <Badge variant={row.original.status === "open" ? "default" : row.original.status === "closed" ? "secondary" : "outline"}>
            {row.original.status}
          </Badge>
        ),
      },
      {
        accessorKey: "priority",
        header: t("dashboard.tickets.priority", "Priority"),
        cell: ({ row }) => (
          <Badge variant={row.original.priority === "high" ? "destructive" : "secondary"}>
            {row.original.priority}
          </Badge>
        ),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => openEdit(row.original)}>
              <Pencil className="size-3.5" />
            </Button>
            <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => void remove(row.original.id)}>
              <Trash2 className="size-3.5" />
            </Button>
          </div>
        ),
      },
    ],
    [t],
  );

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.tickets.title", "Tickets")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.tickets.subtitle", "Manage support tickets.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.tickets.create", "Create Ticket")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.tickets.sheet_create", "Create Ticket")
                : t("dashboard.tickets.sheet_edit", "Edit Ticket")}
            </SheetTitle>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="tk-ticket-number">{t("dashboard.tickets.ticket_number", "Ticket #")}</Label>
              <Input id="tk-ticket-number" value={form.ticket_number} onChange={(e) => setForm((f) => ({ ...f, ticket_number: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tk-title">{t("dashboard.tickets.title_col", "Title")}</Label>
              <Input id="tk-title" value={form.title} onChange={(e) => setForm((f) => ({ ...f, title: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tk-description">{t("dashboard.tickets.description", "Description")}</Label>
              <textarea
                id="tk-description"
                className="flex min-h-[120px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm"
                value={form.description}
                onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tk-status">{t("dashboard.tickets.status", "Status")}</Label>
              <select id="tk-status" className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm" value={form.status} onChange={(e) => setForm((f) => ({ ...f, status: e.target.value }))}>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="closed">Closed</option>
              </select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="tk-priority">{t("dashboard.tickets.priority", "Priority")}</Label>
              <select id="tk-priority" className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm" value={form.priority} onChange={(e) => setForm((f) => ({ ...f, priority: e.target.value }))}>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
              </select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="tk-assigned-to">{t("dashboard.tickets.assigned_to", "Assigned To")}</Label>
              <Input id="tk-assigned-to" type="number" value={form.assigned_to} onChange={(e) => setForm((f) => ({ ...f, assigned_to: e.target.value }))} placeholder="User ID" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tk-brand-id">{t("dashboard.tickets.brand_id", "Brand")}</Label>
              <Input id="tk-brand-id" type="number" value={form.brand_id} onChange={(e) => setForm((f) => ({ ...f, brand_id: e.target.value }))} placeholder="Brand ID" />
            </div>
            <div className="space-y-2">
              <Label htmlFor="tk-due-date">{t("dashboard.tickets.due_date", "Due Date")}</Label>
              <Input id="tk-due-date" type="datetime-local" value={form.due_date} onChange={(e) => setForm((f) => ({ ...f, due_date: e.target.value }))} />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.tickets.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.tickets.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.tickets.confirm_delete", "Delete this ticket?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
