"use client";

import { useEffect, useState } from "react";
import { Loader2 } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

type SystemStatus = {
  php_version: string;
  laravel_version: string;
  database: string;
  cache_driver: string;
  queue_driver: string;
  storage: string;
  environment: string;
  debug_mode: boolean;
  maintenance_mode: boolean;
};

export default function SystemStatusPage() {
  const { t } = useI18n();
  const [status, setStatus] = useState<SystemStatus | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get("/system-status")
      .then((res) => setStatus(res.data as SystemStatus))
      .catch(() => setStatus(null))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  const items = status ? [
    { label: t("dashboard.system_status.php_version", "PHP Version"), value: status.php_version },
    { label: t("dashboard.system_status.laravel_version", "Laravel Version"), value: status.laravel_version },
    { label: t("dashboard.system_status.database", "Database"), value: status.database },
    { label: t("dashboard.system_status.cache_driver", "Cache Driver"), value: status.cache_driver },
    { label: t("dashboard.system_status.queue_driver", "Queue Driver"), value: status.queue_driver },
    { label: t("dashboard.system_status.storage", "Storage"), value: status.storage },
    { label: t("dashboard.system_status.environment", "Environment"), value: status.environment },
    { label: t("dashboard.system_status.debug_mode", "Debug Mode"), value: status.debug_mode ? "On" : "Off" },
    { label: t("dashboard.system_status.maintenance_mode", "Maintenance Mode"), value: status.maintenance_mode ? "On" : "Off" },
  ] : [];

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.system_status.title", "System Status")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.system_status.subtitle", "Current system configuration and health.")}
        </p>
      </div>
      <Card>
        <CardContent className="space-y-3 pt-6">
          {items.map((item) => (
            <div key={item.label} className="flex items-center justify-between border-b border-border/40 pb-2 last:border-0">
              <span className="text-sm text-muted-foreground">{item.label}</span>
              <Badge variant={item.value === "On" ? "destructive" : "secondary"}>{item.value}</Badge>
            </div>
          ))}
        </CardContent>
      </Card>
    </div>
  );
}
