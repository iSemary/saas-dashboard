"use client";

import { useCallback, useEffect, useState } from "react";
import { Loader2, Save } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Label } from "@/components/ui/label";

type ModuleItem = { id: number; name: string; slug: string };
type EntityItem = { id: number; entity_name: string };

export default function ModuleEntitiesPage() {
  const { t } = useI18n();
  const [modules, setModules] = useState<ModuleItem[]>([]);
  const [entities, setEntities] = useState<EntityItem[]>([]);
  const [selectedEntities, setSelectedEntities] = useState<Record<number, number[]>>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    try {
      const [modRes, entRes, mapRes] = await Promise.all([
        api.get("/modules"),
        api.get("/entities"),
        api.get("/module-entities-map"),
      ]);
      setModules(Array.isArray(modRes.data) ? modRes.data : []);
      setEntities(Array.isArray(entRes.data) ? entRes.data : []);
      setSelectedEntities((mapRes.data as Record<number, number[]>) ?? {});
    } catch {
      setModules([]);
      setEntities([]);
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const toggleEntity = (moduleId: number, entityId: number) => {
    setSelectedEntities((prev) => {
      const current = prev[moduleId] ?? [];
      const next = current.includes(entityId)
        ? current.filter((id) => id !== entityId)
        : [...current, entityId];
      return { ...prev, [moduleId]: next };
    });
  };

  const handleSync = async () => {
    setSaving(true);
    try {
      await api.post("/module-entities", { entities: selectedEntities });
      toast.success(t("dashboard.module_entities.toast_synced", "Entities synced."));
    } catch {
      toast.error(t("dashboard.module_entities.toast_sync_error", "Sync failed."));
    } finally {
      setSaving(false);
    }
  };

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
        <h1 className="text-xl font-semibold">{t("dashboard.module_entities.title", "Module Entities")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.module_entities.subtitle", "Assign entities to modules.")}
        </p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>{t("dashboard.module_entities.assign", "Assign Entities")}</CardTitle>
          <CardDescription>{t("dashboard.module_entities.assign_desc", "Select which entities belong to each module.")}</CardDescription>
        </CardHeader>
        <CardContent className="space-y-6">
          {modules.map((mod) => (
            <div key={mod.id} className="space-y-2">
              <Label className="text-sm font-semibold capitalize">{mod.name}</Label>
              <div className="max-h-48 space-y-2 overflow-y-auto rounded-md border border-border/80 p-3">
                {entities.map((ent) => (
                  <label key={ent.id} className="flex cursor-pointer items-center gap-2 text-sm">
                    <input
                      type="checkbox"
                      className="rounded border-input"
                      checked={(selectedEntities[mod.id] ?? []).includes(ent.id)}
                      onChange={() => toggleEntity(mod.id, ent.id)}
                    />
                    <span>{ent.entity_name}</span>
                  </label>
                ))}
              </div>
            </div>
          ))}
          <Button type="button" disabled={saving} onClick={() => void handleSync()}>
            {saving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
            {t("dashboard.module_entities.sync", "Sync")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
