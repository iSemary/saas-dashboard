"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Pencil, Plus, Trash2 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Checkbox } from "@/components/ui/checkbox";
import { Textarea } from "@/components/ui/textarea";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { listConfigurations, type ConfigurationRow } from "@/lib/resources";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";

export default function ConfigurationsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<ConfigurationRow[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({ key: "", value: "", description: "", input_type: "text", is_visible: "1" });

  const load = useCallback(async () => {
    setLoading(true);
    try { setRows(await listConfigurations()); } catch { setRows([]); } finally { setLoading(false); }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setForm({ key: "", value: "", description: "", input_type: "text", is_visible: "1" });
    setSheetOpen(true);
  };

  const openEdit = (row: ConfigurationRow) => {
    setEditingId(row.id);
    setForm({
      key: row.configuration_key,
      value: String(row.configuration_value),
      description: row.description ?? "",
      input_type: row.input_type,
      is_visible: row.is_visible ? "1" : "0",
    });
    setSheetOpen(true);
  };

  const save = async () => {
    setSaving(true);
    try {
      const payload = {
        configuration_key: form.key,
        configuration_value: form.value,
        description: form.description || null,
        input_type: form.input_type,
        is_visible: form.is_visible === "1",
      };
      if (editingId == null) {
        await api.post("/configurations", payload);
        toast.success(t("dashboard.configurations.toast_created", "Configuration created."));
      } else {
        await api.put(`/configurations/${editingId}`, payload);
        toast.success(t("dashboard.configurations.toast_updated", "Configuration updated."));
      }
      setSheetOpen(false);
      await load();
    } catch {
      toast.error(t("dashboard.configurations.toast_save_error", "Save failed."));
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
      await api.delete(`/configurations/${deletingId}`);
      toast.success(t("dashboard.configurations.toast_deleted", "Deleted."));
      await load();
    } catch {
      toast.error(t("dashboard.configurations.toast_delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  // Function to render different input types based on selected input_type
  const renderValueInput = (inputType: string, value: string, onChange: (val: string) => void) => {
    switch (inputType) {
      case "string":
        return (
          <Input
            id="cfg-value"
            type="text"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
      case "integer":
        return (
          <Input
            id="cfg-value"
            type="number"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
      case "boolean":
        return (
          <div className="flex items-center gap-2">
            <Checkbox
              id="cfg-value"
              checked={value === "1" || value === "true"}
              onCheckedChange={(checked) => onChange(checked ? "1" : "0")}
            />
            <Label htmlFor="cfg-value" className="font-normal">
              {t("active", "Active")}
            </Label>
          </div>
        );
      case "html":
        return (
          <Textarea
            id="cfg-value"
            value={value}
            onChange={(e) => onChange(e.target.value)}
            rows={5}
          />
        );
      case "file":
        return (
          <Input
            id="cfg-value"
            type="file"
            onChange={(e) => {
              const file = e.target.files?.[0];
              if (file) onChange(file.name);
            }}
          />
        );
      case "array":
      case "object":
        return (
          <Textarea
            id="cfg-value"
            value={value}
            onChange={(e) => onChange(e.target.value)}
            rows={5}
            placeholder={inputType === "array" ? "[\n  \"item1\",\n  \"item2\"\n]" : "{\n  \"key\": \"value\"\n}"}
          />
        );
      case "email":
        return (
          <Input
            id="cfg-value"
            type="email"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
      case "url":
        return (
          <Input
            id="cfg-value"
            type="url"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
      case "password":
        return (
          <Input
            id="cfg-value"
            type="password"
            value={value}
            onChange={(e) => onChange(e.target.value)}
            placeholder={value ? "••••••" : ""}
          />
        );
      case "phone":
        return (
          <Input
            id="cfg-value"
            type="tel"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
      case "date":
        return (
          <Input
            id="cfg-value"
            type="date"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
      case "time":
        return (
          <Input
            id="cfg-value"
            type="time"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
      case "datetime":
        return (
          <Input
            id="cfg-value"
            type="datetime-local"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
      case "color":
        return (
          <div className="flex items-center gap-2">
            <Input
              id="cfg-value"
              type="color"
              value={value || "#000000"}
              onChange={(e) => onChange(e.target.value)}
              className="h-9 w-16 p-1"
            />
            <span className="text-sm text-muted-foreground">{value || "#000000"}</span>
          </div>
        );
      case "range":
        return (
          <div className="flex items-center gap-2">
            <Input
              id="cfg-value"
              type="range"
              value={value || "50"}
              onChange={(e) => onChange(e.target.value)}
              className="flex-1"
            />
            <span className="text-sm text-muted-foreground w-10 text-right">{value || "50"}</span>
          </div>
        );
      default:
        return (
          <Input
            id="cfg-value"
            type="text"
            value={value}
            onChange={(e) => onChange(e.target.value)}
          />
        );
    }
  };

  const columns: Array<ColumnDef<ConfigurationRow>> = useMemo(
    () => [
      { accessorKey: "id", header: t("dashboard.users.col_id", "ID") },
      { accessorKey: "configuration_key", header: t("dashboard.configurations.key", "Key") },
      {
        accessorKey: "configuration_value",
        header: t("dashboard.configurations.value", "Value"),
        cell: ({ row }) => row.original.is_encrypted ? "••••••" : String(row.original.configuration_value),
      },
      { accessorKey: "input_type", header: t("dashboard.configurations.input_type", "Type") },
      {
        id: "actions",
        header: "",
        cell: ({ row }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => openEdit(row.original)}>
              <Pencil className="size-3.5" />
            </Button>
            {!row.original.is_system && (
              <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => void remove(row.original.id)}>
                <Trash2 className="size-3.5" />
              </Button>
            )}
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
        <h1 className="text-xl font-semibold">{t("dashboard.configurations.title", "Configurations")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.configurations.subtitle", "Manage system configuration values.")}
        </p>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        toolbarActions={(
          <Button type="button" size="sm" className="h-9 gap-1" onClick={openCreate}>
            <Plus className="size-4" />
            {t("dashboard.configurations.create", "Add Configuration")}
          </Button>
        )}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent className="flex w-full max-w-lg flex-col gap-0 sm:max-w-md">
          <SheetHeader>
            <SheetTitle>
              {editingId == null
                ? t("dashboard.configurations.sheet_create", "Add Configuration")
                : t("dashboard.configurations.sheet_edit", "Edit Configuration")}
            </SheetTitle>
            <SheetDescription>{t("dashboard.configurations.sheet_desc", "Set configuration key and value.")}</SheetDescription>
          </SheetHeader>
          <div className="flex min-h-0 flex-1 flex-col gap-4 overflow-y-auto px-4 pb-4">
            <div className="space-y-2">
              <Label htmlFor="cfg-key">{t("dashboard.configurations.key", "Key")}</Label>
              <Input id="cfg-key" value={form.key} onChange={(e) => setForm((f) => ({ ...f, key: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="cfg-desc">{t("dashboard.configurations.description", "Description")}</Label>
              <Input id="cfg-desc" value={form.description} onChange={(e) => setForm((f) => ({ ...f, description: e.target.value }))} />
            </div>
            <div className="space-y-2">
              <Label htmlFor="cfg-type">{t("dashboard.configurations.input_type", "Input Type")}</Label>
              <select id="cfg-type" className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm" value={form.input_type} onChange={(e) => setForm((f) => ({ ...f, input_type: e.target.value }))}>
                <option value="string">String</option>
                <option value="integer">Integer</option>
                <option value="boolean">Boolean</option>
                <option value="html">HTML</option>
                <option value="file">File</option>
                <option value="array">Array</option>
                <option value="object">Object</option>
                <option value="email">Email</option>
                <option value="url">URL</option>
                <option value="password">Password</option>
                <option value="phone">Phone</option>
                <option value="date">Date</option>
                <option value="time">Time</option>
                <option value="datetime">DateTime</option>
                <option value="color">Color</option>
                <option value="range">Range</option>
              </select>
            </div>
            <div className="space-y-2">
              <Label htmlFor="cfg-value">{t("dashboard.configurations.value", "Value")}</Label>
              {renderValueInput(form.input_type, form.value, (val) => setForm((f) => ({ ...f, value: val })))}
            </div>
          </div>
          <SheetFooter className="border-t border-border/80 px-4 py-3">
            <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
              {t("dashboard.actions.cancel", "Cancel")}
            </Button>
            <Button type="button" disabled={saving} onClick={() => void save()}>
              {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.configurations.save", "Save")}
            </Button>
          </SheetFooter>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.configurations.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.configurations.confirm_delete", "Delete this configuration?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
