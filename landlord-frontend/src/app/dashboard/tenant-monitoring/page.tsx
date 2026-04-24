"use client";

import { useEffect, useState } from "react";
import { Loader2, RefreshCw } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

type TenantMonitor = {
  tenant: { id: number; name: string; domain: string };
  status: "healthy" | "warning" | "error";
  database_size?: string;
  user_count?: number;
  last_activity?: string;
  details?: Record<string, string>;
};

export default function TenantMonitoringPage() {
  const { t } = useI18n();
  const [tenants, setTenants] = useState<TenantMonitor[]>([]);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    try {
      const res = await api.get("/tenant-monitoring");
      setTenants(Array.isArray(res.data) ? (res.data as TenantMonitor[]) : []);
    } catch {
      setTenants([]);
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
            <h1 className="text-xl font-semibold">{t("dashboard.tenant_monitoring.title", "Tenant Monitoring")}</h1>
            <p className="mt-1 text-sm text-muted-foreground">
              {t("dashboard.tenant_monitoring.subtitle", "Monitor tenant health and activity.")}
            </p>
          </div>
          <Button type="button" variant="outline" size="sm" onClick={() => void load()}>
            <RefreshCw className="size-4" />
          </Button>
        </div>
      </div>

      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {tenants.map((item) => (
          <Card key={item.tenant.id}>
            <CardHeader className="pb-2">
              <CardTitle className="flex items-center justify-between text-sm">
                <span>{item.tenant.name}</span>
                <Badge
                  variant={item.status === "healthy" ? "default" : item.status === "warning" ? "secondary" : "destructive"}
                >
                  {item.status}
                </Badge>
              </CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              <p className="font-mono text-xs text-muted-foreground">{item.tenant.domain}</p>
              {item.database_size && (
                <div className="flex justify-between text-xs">
                  <span className="text-muted-foreground">{t("dashboard.tenant_monitoring.db_size", "DB Size")}</span>
                  <span>{item.database_size}</span>
                </div>
              )}
              {item.user_count != null && (
                <div className="flex justify-between text-xs">
                  <span className="text-muted-foreground">{t("dashboard.tenant_monitoring.users", "Users")}</span>
                  <span>{item.user_count}</span>
                </div>
              )}
              {item.last_activity && (
                <div className="flex justify-between text-xs">
                  <span className="text-muted-foreground">{t("dashboard.tenant_monitoring.last_activity", "Last Activity")}</span>
                  <span>{new Date(item.last_activity).toLocaleDateString()}</span>
                </div>
              )}
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
