"use client";

import { useEffect, useState } from "react";
import { Loader2, RefreshCw } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

type EnvDiffData = {
  status: string;
  message: string;
  env_count: number;
  env_example_count?: number;
  missing_in_env: string[];
  missing_in_env_example: string[];
};

export default function EnvDiffPage() {
  const { t } = useI18n();
  const [data, setData] = useState<EnvDiffData | null>(null);
  const [loading, setLoading] = useState(true);

  const load = async () => {
    setLoading(true);
    try {
      const res = await api.get("/env-diff");
      setData(res.data as EnvDiffData);
    } catch {
      setData(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    void load();
  }, []);

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  const isSuccess = data?.status === "success";

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-xl font-semibold">{t("dashboard.env_diff.title", "Env Diff")}</h1>
            <p className="mt-1 text-sm text-muted-foreground">
              {t("dashboard.env_diff.subtitle", "Compare .env and .env.example for missing keys.")}
            </p>
          </div>
          <Button type="button" variant="outline" size="sm" onClick={() => void load()}>
            <RefreshCw className="size-4" />
          </Button>
        </div>
      </div>

      {data && (
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <Badge variant={isSuccess ? "default" : "destructive"}>
                {isSuccess ? t("dashboard.env_diff.success", "Success") : t("dashboard.env_diff.error", "Error")}
              </Badge>
              <span className="text-sm font-normal">{data.message}</span>
            </CardTitle>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="flex gap-4 text-sm">
              <span className="text-muted-foreground">
                {t("dashboard.env_diff.total_keys", "Total keys")}: <strong>{data.env_count}</strong>
              </span>
              {!isSuccess && data.env_example_count != null && (
                <span className="text-muted-foreground">
                  {t("dashboard.env_diff.example_keys", ".env.example keys")}: <strong>{data.env_example_count}</strong>
                </span>
              )}
            </div>

            {!isSuccess && (
              <div className="grid gap-4 sm:grid-cols-2">
                {data.missing_in_env.length > 0 && (
                  <div className="rounded-md border border-destructive/40 p-3">
                    <h3 className="mb-2 text-sm font-semibold text-destructive">
                      {t("dashboard.env_diff.missing_in_env", "Missing in .env")}
                    </h3>
                    <ul className="space-y-1">
                      {data.missing_in_env.map((key) => (
                        <li key={key} className="font-mono text-xs">{key}</li>
                      ))}
                    </ul>
                  </div>
                )}
                {data.missing_in_env_example.length > 0 && (
                  <div className="rounded-md border border-yellow-500/40 p-3">
                    <h3 className="mb-2 text-sm font-semibold text-yellow-600">
                      {t("dashboard.env_diff.missing_in_example", "Missing in .env.example")}
                    </h3>
                    <ul className="space-y-1">
                      {data.missing_in_env_example.map((key) => (
                        <li key={key} className="font-mono text-xs">{key}</li>
                      ))}
                    </ul>
                  </div>
                )}
              </div>
            )}
          </CardContent>
        </Card>
      )}
    </div>
  );
}
