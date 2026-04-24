"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Card, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
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
import {
  createPermissionGroup,
  deletePermissionGroup,
  fetchPermissionGroup,
  listPermissionGroups,
  type PermissionGroup,
  updatePermissionGroup,
} from "@/lib/permission-groups";
import { listPermissions } from "@/lib/resources";
import { useI18n } from "@/context/i18n-context";

type Row = PermissionGroup;

export default function PermissionGroupsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<Row[]>([]);
  const [perms, setPerms] = useState<Array<{ id: number; name: string }>>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [name, setName] = useState("");
  const [slug, setSlug] = useState("");
  const [description, setDescription] = useState("");
  const [selectedPermIds, setSelectedPermIds] = useState<Set<number>>(new Set());

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const [groups, permissions] = await Promise.all([listPermissionGroups(), listPermissions()]);
      setRows(groups);
      setPerms(
        (permissions as Array<{ id: number; name: string }>).map((p) => ({ id: p.id, name: p.name })),
      );
    } catch {
      toast.error(t("dashboard.permission_groups.toast_load_list_error", "Could not load permission groups."));
      setRows([]);
    } finally {
      setLoading(false);
    }
  }, [t]);

  useEffect(() => {
    void load();
  }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setName("");
    setSlug("");
    setDescription("");
    setSelectedPermIds(new Set());
    setSheetOpen(true);
  };

  const openEdit = async (id: number) => {
    setEditingId(id);
    setSheetOpen(true);
    try {
      const g = await fetchPermissionGroup(id);
      setName(g.name);
      setSlug(g.slug);
      setDescription(g.description ?? "");
      setSelectedPermIds(new Set((g.permissions ?? []).map((p) => p.id)));
    } catch {
      toast.error(t("dashboard.permission_groups.toast_load_group_error", "Could not load group."));
      setSheetOpen(false);
    }
  };

  const save = async () => {
    if (!name.trim()) {
      toast.error(t("dashboard.permission_groups.name_required", "Name is required."));
      return;
    }
    setSaving(true);
    try {
      const payload = {
        name: name.trim(),
        slug: slug.trim() || undefined,
        description: description.trim() || null,
        permission_ids: [...selectedPermIds],
      };
      if (editingId == null) {
        await createPermissionGroup(payload);
        toast.success(t("dashboard.permission_groups.toast_created", "Permission group created."));
      } else {
        await updatePermissionGroup(editingId, payload);
        toast.success(t("dashboard.permission_groups.toast_updated", "Permission group updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.permission_groups.toast_save_error", "Save failed."));
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
      await deletePermissionGroup(deletingId);
      toast.success(t("dashboard.permission_groups.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.permission_groups.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const columns: Array<ColumnDef<Row>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "name", header: t("dashboard.users.col_name", "Name") },
      { accessorKey: "slug", header: t("dashboard.permission_groups.col_slug", "Slug") },
      {
        accessorKey: "permissions_count",
        header: t("dashboard.permission_groups.col_permissions", "Permissions"),
        cell: ({ row }) => row.original.permissions_count ?? row.original.permissions?.length ?? "—",
      },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => void openEdit(row.original.id)}>
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
        <span className="text-sm">{t("dashboard.permission_groups.loading", "Loading…")}</span>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <Card className="border-border/80 bg-muted/30">
        <CardHeader>
          <CardTitle className="text-xl">{t("dashboard.permission_groups.title", "Permission groups")}</CardTitle>
          <CardDescription>
            {t(
              "dashboard.permission_groups.card_desc",
              "Bundle permissions into named groups, then assign groups to users or roles.",
            )}
          </CardDescription>
        </CardHeader>
      </Card>

      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.permission_groups.new_group", "New group")}
          </Button>
        )}
      />

      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-xl">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.permission_groups.sheet_create_title", "Create permission group")
                : t("dashboard.permission_groups.sheet_edit_title", "Edit permission group")}
            </SheetTitle>
            <SheetDescription>
              {t(
                "dashboard.permission_groups.sheet_desc",
                "Choose a name and select which permissions belong to this group.",
              )}
            </SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="pg-name">{t("dashboard.permission_groups.name", "Name")}</Label>
              <Input
                id="pg-name"
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder={t("dashboard.permission_groups.placeholder_name", "e.g. Content editors")}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="pg-slug">{t("dashboard.permission_groups.slug", "Slug (optional)")}</Label>
              <Input
                id="pg-slug"
                value={slug}
                onChange={(e) => setSlug(e.target.value)}
                placeholder={t("dashboard.permission_groups.placeholder_slug", "auto from name if empty")}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="pg-desc">{t("dashboard.permission_groups.description", "Description")}</Label>
              <Input
                id="pg-desc"
                value={description}
                onChange={(e) => setDescription(e.target.value)}
                placeholder={t("dashboard.permission_groups.placeholder_desc", "Optional")}
              />
            </div>
            <div className="space-y-2">
              <Label>{t("dashboard.permission_groups.permissions", "Permissions")}</Label>
              <div className="max-h-64 space-y-2 overflow-y-auto rounded-md border border-border/80 p-3">
                {perms.map((p) => (
                  <label key={p.id} className="flex cursor-pointer items-center gap-2 text-sm">
                    <input
                      type="checkbox"
                      className="rounded border-input"
                      checked={selectedPermIds.has(p.id)}
                      onChange={(e) => {
                        setSelectedPermIds((prev) => {
                          const next = new Set(prev);
                          if (e.target.checked) next.add(p.id);
                          else next.delete(p.id);
                          return next;
                        });
                      }}
                    />
                    <span className="font-mono text-xs">{p.name}</span>
                  </label>
                ))}
              </div>
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.permission_groups.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.permission_groups.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.permission_groups.confirm_delete_full", "Delete this permission group? Assignments to users and roles will be removed.")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
