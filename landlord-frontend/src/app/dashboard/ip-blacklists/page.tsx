"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2 } from "lucide-react";
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
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";

type IpBlacklistRow = {
  id: number;
  ip_address: string;
  created_at?: string;
};

export default function IpBlacklistsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<IpBlacklistRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [ipAddress, setIpAddress] = useState("");

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const res = await api.get("/ip-blacklists");
      setRows(Array.isArray(res.data) ? (res.data as IpBlacklistRow[]) : []);
    } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setIpAddress("");
    setSheetOpen(true);
  };

  const openEdit = (row: IpBlacklistRow) => {
    setEditingId(row.id);
    setIpAddress(row.ip_address);
    setSheetOpen(true);
  };

  const save = async () => {
    if (!ipAddress.trim()) return;
    setSaving(true);
    try {
      if (editingId == null) {
        await api.post("/ip-blacklists", { ip_address: ipAddress });
        toast.success(t("dashboard.ip_blacklists.toast_created", "IP blacklisted."));
      } else {
        await api.put(`/ip-blacklists/${editingId}`, { ip_address: ipAddress });
        toast.success(t("dashboard.ip_blacklists.toast_updated", "Updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.ip_blacklists.toast_save_error", "Save failed."));
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
      await api.delete(`/ip-blacklists/${deletingId}`);
      toast.success(t("dashboard.ip_blacklists.toast_deleted", "Removed."));
      await load();
    } catch {
      toast.error(t("dashboard.ip_blacklists.toast_delete_error", "Could not remove."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<IpBlacklistRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "ip_address", header: t("dashboard.ip_blacklists.ip_address", "IP Address") },
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
    [t, openEdit, remove],
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
        <h1 className="text-xl font-semibold">{t("dashboard.ip_blacklists.title", "IP Blacklists")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.ip_blacklists.subtitle", "Manage blocked IP addresses.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.ip_blacklists.create", "Add IP")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.ip_blacklists.sheet_create", "Add IP")
                : t("dashboard.ip_blacklists.sheet_edit", "Edit IP")}
            </SheetTitle>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="ip-addr">{t("dashboard.ip_blacklists.ip_address", "IP Address")}</Label>
              <Input id="ip-addr" value={ipAddress} onChange={(e) => setIpAddress(e.target.value)} placeholder="192.168.1.1" />
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.ip_blacklists.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.ip_blacklists.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.ip_blacklists.confirm_delete", "Remove this IP?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
