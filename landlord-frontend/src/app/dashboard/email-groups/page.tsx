"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listEmailGroups, createEmailGroup, deleteEmailGroup, type EmailGroupRow } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

export default function EmailGroupsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<EmailGroupRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [name, setName] = useState("");

  const load = useCallback(async () => {
    setLoading(true);
    try { setRows(await listEmailGroups()); } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const save = async () => {
    if (!name.trim()) return;
    setSaving(true);
    try {
      await createEmailGroup({ name: name.trim() });
      toast.success(t("dashboard.email_groups.toast_created", "Group created."));
      setSheetOpen(false);
      setName("");
      await load();
    } catch {
      toast.error(t("dashboard.email_groups.toast_save_error", "Could not create group."));
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
      await deleteEmailGroup(deletingId);
      toast.success(t("dashboard.email_groups.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.email_groups.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<EmailGroupRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      {
        accessorKey: "recipients_count",
        header: t("dashboard.email_groups.recipients", "Recipients"),
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => void remove(row.original.id)}>
            <Trash2 className="size-3.5" />
          </Button>
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
        <h1 className="text-xl font-semibold">{t("dashboard.email_groups.title", "Email Groups")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.email_groups.subtitle", "Organize recipients into groups.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={() => { setName(""); setSheetOpen(true); }}>
            <Plus className="size-4" />
            {t("dashboard.email_groups.create", "Create Group")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>{t("dashboard.email_groups.sheet_create", "Create Group")}</SheetTitle>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="eg-name">{t("dashboard.users.col_name", "Name")}</Label>
              <Input id="eg-name" value={name} onChange={(e) => setName(e.target.value)} />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving || !name.trim()} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.email_groups.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.email_groups.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.email_groups.confirm_delete", "Delete this group?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
