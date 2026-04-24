"use client";

import { useEffect, useState } from "react";
import { Loader2, Save } from "lucide-react";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getSettings, updateSettings } from "@/lib/tenant-resources";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function SettingsPage() {
  const { t } = useI18n();
  const [settings, setSettings] = useState<Record<string, string>>({});
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    getSettings().then((d) => setSettings(d as Record<string, string>)).finally(() => setLoading(false));
  }, []);

  const save = async () => {
    setSaving(true);
    try {
      await updateSettings({ settings });
      toast.success(t("dashboard.settings.saved", "Settings saved."));
    } catch {
      toast.error(t("dashboard.settings.save_error", "Failed to save."));
    } finally {
      setSaving(false);
    }
  };

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.settings.title", "General Settings")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.settings.subtitle", "Configure your tenant settings")}</p>
      </div>
      <Card>
        <CardHeader><CardTitle>{t("dashboard.settings.general", "General")}</CardTitle></CardHeader>
        <CardContent className="space-y-4">
          {Object.entries(settings).map(([key, value]) => (
            <div key={key} className="space-y-2">
              <Label>{key}</Label>
              <Input value={value} onChange={(e) => setSettings((s) => ({ ...s, [key]: e.target.value }))} />
            </div>
          ))}
          <Button type="button" disabled={saving} onClick={() => void save()}>
            {saving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
            {t("dashboard.crud.save", "Save")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
