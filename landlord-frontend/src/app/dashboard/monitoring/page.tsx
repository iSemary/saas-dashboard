"use client";

import { useEffect, useState } from "react";
import { Loader2, RefreshCw } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type MonitoringData = Record<string, Record<string, string | number>>;

export default function MonitoringPage() {
  const { t } = useI18n();
  const [data, setData] = useState<MonitoringData | null>(null);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    try {
      const res = await api.get("/monitoring");
      setData(res.data as MonitoringData);
    } catch {
      setData(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { void load(); }, []);

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
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-xl font-semibold">{t("dashboard.monitoring.title", "Monitoring")}</h1>
            <p className="mt-1 text-sm text-muted-foreground">
              {t("dashboard.monitoring.subtitle", "Platform monitoring dashboard.")}
            </p>
          </div>
          <Button type="button" variant="outline" size="sm" onClick={() => void load()}>
            <RefreshCw className="size-4" />
          </Button>
        </div>
      </div>

      {data && Object.entries(data).map(([section, metrics]) => (
        <Card key={section}>
          <CardHeader>
            <CardTitle className="text-sm capitalize">{section.replace(/_/g, " ")}</CardTitle>
          </CardHeader>
          <CardContent className="space-y-2">
            {Object.entries(metrics).map(([key, value]) => (
              <div key={key} className="flex items-center justify-between border-b border-border/40 pb-1 last:border-0">
                <span className="text-xs text-muted-foreground capitalize">{key.replace(/_/g, " ")}</span>
                <span className="text-xs font-semibold">{String(value)}</span>
              </div>
            ))}
          </CardContent>
        </Card>
      ))}
    </div>
  );
}
