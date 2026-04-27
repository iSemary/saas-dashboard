"use client";

import { useCallback, useEffect, useMemo, useState } from "react";
import { ColumnDef } from "@tanstack/react-table";
import { Loader2, Plus, Pencil, Trash2, Users, Briefcase, Calculator, ShoppingCart, Package, BarChart3 } from "lucide-react";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { SlugInput } from "@/components/ui/slug-input";
import { Checkbox } from "@/components/ui/checkbox";
import {
  Sheet,
  SheetContent,
  SheetDescription,
  SheetFooter,
  SheetHeader,
  SheetTitle,
} from "@/components/ui/sheet";
import { ConfirmDialog } from "@/components/ui/confirm-dialog";
import { useI18n } from "@/context/i18n-context";
import { Blur } from "@/components/animate-ui/primitives/effects/blur";
import { listBrands, createBrand, updateBrand, deleteBrand, getAvailableModules, getBrandWithModules } from "@/lib/tenant-resources";
import type { TableParams } from "@/lib/tenant-resources";
import { cn } from "@/lib/utils";

type Brand = { id: number; name: string; slug?: string; domain?: string; is_active?: boolean };
type ModuleTheme = { primary_color?: string; secondary_color?: string };
type Module = { id: number; module_key: string; name: string; description?: string; icon?: string; theme?: ModuleTheme };

const MODULE_ICONS: Record<string, React.ComponentType<{ className?: string }>> = {
  crm: Users,
  hr: Briefcase,
  accounting: Calculator,
  sales: ShoppingCart,
  inventory: Package,
  reporting: BarChart3,
};

export default function BrandsPage() {
  const { t } = useI18n();
  const [rows, setRows] = useState<Brand[]>([]);
  const [modules, setModules] = useState<Module[]>([]);
  const [loading, setLoading] = useState(true);
  const [sheetOpen, setSheetOpen] = useState(false);
  const [editingId, setEditingId] = useState<number | null>(null);
  const [saving, setSaving] = useState(false);
  const [confirmOpen, setConfirmOpen] = useState(false);
  const [deletingId, setDeletingId] = useState<number | null>(null);
  const [form, setForm] = useState({
    name: "",
    slug: "",
    domain: "",
    is_active: "1",
    selectedModules: [] as string[],
  });
  const [tableMeta, setTableMeta] = useState<{ current_page: number; last_page: number; per_page: number; total: number } | undefined>(undefined);

  const load = useCallback(async (params?: TableParams) => {
    setLoading(true);
    try {
      const [brands, availableMods] = await Promise.all([
        listBrands(params),
        getAvailableModules() as Promise<Module[]>,
      ]);
      // Handle both array and paginated response
      if (Array.isArray(brands)) {
        setRows(brands);
        setTableMeta(undefined);
      } else {
        setRows(brands.data ?? []);
        setTableMeta(brands.meta);
      }
      setModules(availableMods);
    } catch {
      setRows([]);
      setTableMeta(undefined);
      toast.error("Failed to load data");
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => {
    void load({ page: 1, per_page: 10 });
  }, [load]);

  const handleTableChange = useCallback((params: { page: number; perPage: number; search: string; sortBy: string | null; sortDirection: 'asc' | 'desc' }) => {
    const tableParams: TableParams = {
      page: params.page,
      per_page: params.perPage,
      search: params.search || undefined,
      sort_by: params.sortBy || undefined,
      sort_direction: params.sortDirection,
    };
    void load(tableParams);
  }, [load]);

  const openCreate = () => {
    setEditingId(null);
    setForm({
      name: "",
      slug: "",
      domain: "",
      is_active: "1",
      selectedModules: [],
    });
    setSheetOpen(true);
  };

  const openEdit = async (row: Brand) => {
    setEditingId(row.id);
    // Load brand with modules
    try {
      const brandData = await getBrandWithModules(row.id) as Brand & { modules?: Array<{ module_key: string }> };
      const selectedModuleKeys = brandData.modules?.map(m => m.module_key) || [];
      setForm({
        name: row.name,
        slug: row.slug ?? "",
        domain: row.domain ?? "",
        is_active: row.is_active ? "1" : "0",
        selectedModules: selectedModuleKeys,
      });
    } catch {
      setForm({
        name: row.name,
        slug: row.slug ?? "",
        domain: row.domain ?? "",
        is_active: row.is_active ? "1" : "0",
        selectedModules: [],
      });
    }
    setSheetOpen(true);
  };

  const save = async () => {
    setSaving(true);
    try {
      // Validate reserved slugs
      const reservedSlugs = ['new', 'create', 'edit', 'show', 'crm', 'hr', 'pos', 'survey', 'inventory', 'sales'];
      if (form.slug && reservedSlugs.includes(form.slug.toLowerCase())) {
        toast.error(t("dashboard.brands.reserved_slug", "This slug is reserved and cannot be used."));
        setSaving(false);
        return;
      }

      const payload = {
        name: form.name,
        slug: form.slug || undefined,
        domain: form.domain || undefined,
        is_active: form.is_active === "1",
        modules: form.selectedModules,
      };
      if (editingId == null) {
        await createBrand(payload);
        toast.success(t("dashboard.crud.created", "Created successfully."));
      } else {
        await updateBrand(editingId, payload);
        toast.success(t("dashboard.crud.updated", "Updated successfully."));
      }
      setSheetOpen(false);
      await load({ page: 1, per_page: tableMeta?.per_page ?? 10 });
    } catch {
      toast.error(t("dashboard.crud.save_error", "Save failed."));
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
      await deleteBrand(deletingId);
      toast.success(t("dashboard.crud.deleted", "Deleted."));
      await load({ page: 1, per_page: tableMeta?.per_page ?? 10 });
    } catch {
      toast.error(t("dashboard.crud.delete_error", "Could not delete."));
    } finally {
      setDeletingId(null);
    }
  };

  const toggleModule = (moduleKey: string) => {
    setForm((f) => ({
      ...f,
      selectedModules: f.selectedModules.includes(moduleKey)
        ? f.selectedModules.filter((m) => m !== moduleKey)
        : [...f.selectedModules, moduleKey],
    }));
  };

  const columns = useMemo((): Array<ColumnDef<Brand>> => {
    return [
      { accessorKey: "id", header: t("dashboard.table.id", "ID"), meta: { searchable: true, sortable: true } },
      { accessorKey: "name", header: t("dashboard.table.name", "Name"), meta: { searchable: true, sortable: true } },
      { accessorKey: "slug", header: t("dashboard.table.slug", "Slug"), meta: { searchable: true, sortable: true } },
      { accessorKey: "domain", header: t("dashboard.table.domain", "Domain"), meta: { searchable: true, sortable: true } },
      {
        id: "actions",
        header: "",
        cell: ({ row }: { row: { original: Brand } }) => (
          <div className="flex justify-end gap-1">
            <Button type="button" variant="outline" size="sm" className="h-8" onClick={() => void openEdit(row.original)}>
              <Pencil className="size-3.5" />
            </Button>
            <Button type="button" variant="outline" size="sm" className="h-8 text-destructive" onClick={() => void remove(row.original.id)}>
              <Trash2 className="size-3.5" />
            </Button>
          </div>
        ),
      },
    ];
  }, [t]);

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4 flex items-center justify-between gap-4">
        <div>
          <h1 className="text-xl font-semibold">{t("dashboard.brands.title", "Brands")}</h1>
          <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.brands.subtitle", "Manage your brands")}</p>
        </div>
        <Button type="button" size="sm" className="h-9 gap-1 shrink-0" onClick={openCreate}>
          <Plus className="size-4" />
          {t("dashboard.brands.create", "New Brand")}
        </Button>
      </div>
      <DataTable
        columns={columns}
        data={rows}
        enableExport={true}
        searchable={true}
        serverSide={true}
        meta={tableMeta}
        loading={loading}
        onTableChange={handleTableChange}
      />
      <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
        <SheetContent 
          className="flex w-full max-w-3xl flex-col gap-0 sm:max-w-[50vw] max-h-screen"
          resizable={true}
          defaultWidth={typeof window !== 'undefined' ? window.innerWidth * 0.5 : 600}
          minWidth={320}
          maxWidth={typeof window !== 'undefined' ? window.innerWidth * 0.8 : 1200}
        >
          <Blur inView inViewOnce delay={100} className="flex flex-col h-full overflow-hidden">
            <SheetHeader>
              <SheetTitle>
                {editingId == null
                  ? t("dashboard.crud.create", "Create Brand")
                  : t("dashboard.crud.edit", "Edit Brand")}
              </SheetTitle>
              <SheetDescription>
                {t("dashboard.crud.sheet_desc", "Fill in the details and select modules.")}
              </SheetDescription>
            </SheetHeader>
            <div className="flex min-h-0 flex-1 flex-col gap-6 overflow-y-auto px-4 pb-4">
              {/* Basic Info */}
              <div className="space-y-4">
                <h3 className="text-sm font-medium text-muted-foreground">{t("dashboard.brands.basic_info", "Basic Information")}</h3>
                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="field-name">{t("dashboard.brands.name", "Name")} *</Label>
                    <Input
                      id="field-name"
                      value={form.name}
                      onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))}
                      placeholder="Acme Corp"
                      required
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="field-slug">{t("dashboard.brands.slug", "Slug")}</Label>
                    <SlugInput
                      id="field-slug"
                      value={form.slug}
                      onChange={(v) => setForm((f) => ({ ...f, slug: v }))}
                      sourceValue={form.name}
                      placeholder="acme-corp"
                    />
                  </div>
                </div>
                <div className="grid gap-4 sm:grid-cols-2">
                  <div className="space-y-2">
                    <Label htmlFor="field-domain">{t("dashboard.brands.domain", "Domain")}</Label>
                    <Input
                      id="field-domain"
                      type="url"
                      value={form.domain}
                      onChange={(e) => setForm((f) => ({ ...f, domain: e.target.value }))}
                      placeholder="https://acme.com"
                    />
                  </div>
                  <div className="space-y-2">
                    <Label htmlFor="field-active">{t("dashboard.brands.active", "Active")}</Label>
                    <select
                      id="field-active"
                      className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
                      value={form.is_active}
                      onChange={(e) => setForm((f) => ({ ...f, is_active: e.target.value }))}
                    >
                      <option value="1">{t("dashboard.yes", "Yes")}</option>
                      <option value="0">{t("dashboard.no", "No")}</option>
                    </select>
                  </div>
                </div>
              </div>

              {/* Modules Selection */}
              <div className="space-y-4">
                <h3 className="text-sm font-medium text-muted-foreground">{t("dashboard.brands.modules", "Module Assignments")}</h3>
                <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                  {modules.map((module, index) => {
                    const IconComponent = MODULE_ICONS[module.module_key] || Package;
                    const isSelected = form.selectedModules.includes(module.module_key);
                    const primaryColor = module.theme?.primary_color;
                    const hasTheme = !!primaryColor;

                    // Dynamic styles based on module theme
                    const cardStyle: React.CSSProperties = isSelected && hasTheme
                      ? {
                          borderColor: primaryColor,
                          backgroundColor: `${primaryColor}15`, // 10% opacity
                        }
                      : {};

                    const titleStyle: React.CSSProperties = isSelected && hasTheme
                      ? { color: primaryColor }
                      : {};

                    const iconBgStyle: React.CSSProperties = isSelected && hasTheme
                      ? { backgroundColor: `${primaryColor}25` } // 15% opacity
                      : {};

                    const iconStyle: React.CSSProperties = isSelected && hasTheme
                      ? { color: primaryColor }
                      : {};

                    const descStyle: React.CSSProperties = isSelected && hasTheme
                      ? { color: primaryColor, opacity: 0.8 }
                      : {};

                    return (
                      <Blur key={module.id} inView inViewOnce delay={150 + index * 50}>
                        <div
                          onClick={() => toggleModule(module.module_key)}
                          style={cardStyle}
                          className={cn(
                            "group relative cursor-pointer rounded-lg border-2 p-4 transition-all duration-200 hover:scale-[1.02] hover:shadow-md",
                            isSelected
                              ? hasTheme
                                ? "border-current"
                                : "bg-blue-500/10 border-blue-200"
                              : "border-border bg-background hover:border-muted-foreground/30"
                          )}
                        >
                          <div className="flex flex-col items-center text-center gap-3">
                            <div className="flex items-center justify-between w-full">
                              <h4
                                style={titleStyle}
                                className={cn("font-medium text-sm", isSelected && !hasTheme && "text-blue-600")}
                              >
                                {module.name}
                              </h4>
                              <Checkbox
                                checked={isSelected}
                                onCheckedChange={() => toggleModule(module.module_key)}
                                className="pointer-events-none"
                              />
                            </div>
                            <div
                              style={iconBgStyle}
                              className={cn(
                                "flex size-12 items-center justify-center rounded-xl transition-colors",
                                isSelected && !hasTheme ? "bg-blue-500/20" : "bg-muted group-hover:bg-muted/80"
                              )}
                            >
                              <span style={iconStyle}>
                                <IconComponent
                                  className={cn("size-6", isSelected && !hasTheme ? "text-blue-600" : "text-muted-foreground")}
                                />
                              </span>
                            </div>
                            {module.description && (
                              <p
                                style={descStyle}
                                className={cn("text-xs line-clamp-2", isSelected && !hasTheme ? "text-blue-600/80" : "text-muted-foreground")}
                              >
                                {module.description}
                              </p>
                            )}
                          </div>
                        </div>
                      </Blur>
                    );
                  })}
                </div>
                {modules.length === 0 && (
                  <p className="text-sm text-muted-foreground text-center py-4">
                    {t("dashboard.brands.no_modules", "No modules available")}
                  </p>
                )}
              </div>
            </div>
            <SheetFooter className="border-t border-border/80 px-4 py-3">
              <Button type="button" variant="outline" onClick={() => setSheetOpen(false)}>
                {t("dashboard.actions.cancel", "Cancel")}
              </Button>
              <Button type="button" disabled={saving || !form.name} onClick={() => void save()}>
                {saving ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.crud.save", "Save")}
              </Button>
            </SheetFooter>
          </Blur>
        </SheetContent>
      </Sheet>
      <ConfirmDialog
        open={confirmOpen}
        onOpenChange={setConfirmOpen}
        title={t("dashboard.crud.confirm_delete_title", "Confirm Delete")}
        description={t("dashboard.crud.confirm_delete", "Delete this item?")}
        onConfirm={handleConfirmDelete}
        confirmText={t("dashboard.actions.delete", "Delete")}
        cancelText={t("dashboard.actions.cancel", "Cancel")}
      />
    </div>
  );
}
