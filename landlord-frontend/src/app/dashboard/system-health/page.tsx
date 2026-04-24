"use client";

import { useEffect, useState } from "react";
import { Loader2, RefreshCw } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

type HealthCheck = {
  name: string;
  status: "ok" | "warning" | "error";
  message: string;
  details?: Record<string, string>;
};

export default function SystemHealthPage() {
  const { t } = useI18n();
  const [checks, setChecks] = useState<HealthCheck[]>([]);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    try {
      const res = await api.get("/system-health");
      setChecks(Array.isArray(res.data) ? (res.data as HealthCheck[]) : []);
    } catch {
      setChecks([]);
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
            <h1 className="text-xl font-semibold">{t("dashboard.system_health.title", "System Health")}</h1>
            <p className="mt-1 text-sm text-muted-foreground">
              {t("dashboard.system_health.subtitle", "Health checks for all system components.")}
            </p>
          </div>
          <Button type="button" variant="outline" size="sm" onClick={() => void load()}>
            <RefreshCw className="size-4" />
          </Button>
        </div>
      </div>

      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {checks.map((check) => (
          <Card key={check.name}>
            <CardHeader className="pb-2">
              <CardTitle className="flex items-center justify-between text-sm">
                <span className="capitalize">{check.name.replace(/_/g, " ")}</span>
                <Badge
                  variant={check.status === "ok" ? "default" : check.status === "warning" ? "secondary" : "destructive"}
                >
                  {check.status}
                </Badge>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <p className="text-xs text-muted-foreground">{check.message}</p>
              {check.details && (
                <div className="mt-2 space-y-1">
                  {Object.entries(check.details).map(([k, v]) => (
                    <div key={k} className="flex justify-between text-xs">
                      <span className="text-muted-foreground capitalize">{k.replace(/_/g, " ")}</span>
                      <span className="font-mono">{v}</span>
                    </div>
                  ))}
                </div>
              )}
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
