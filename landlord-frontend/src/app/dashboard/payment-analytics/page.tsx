"use client";

import { useEffect, useState } from "react";
import { Loader2 } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

type PaymentAnalytics = {
  total_revenue: number;
  monthly_revenue: Array<{ month: string; revenue: number }>;
  by_method: Record<string, number>;
  by_status: Record<string, number>;
};

export default function PaymentAnalyticsPage() {
  const { t } = useI18n();
  const [data, setData] = useState<PaymentAnalytics | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get("/payment-analytics")
      .then((res) => setData(res.data as PaymentAnalytics))
      .catch(() => setData(null))
      .finally(() => setLoading(false));
  }, []);

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
        <h1 className="text-xl font-semibold">{t("dashboard.payment_analytics.title", "Payment Analytics")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.payment_analytics.subtitle", "Overview of payment and revenue data.")}
        </p>
      </div>

      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        <Card>
          <CardHeader>
            <CardTitle className="text-sm">{t("dashboard.payment_analytics.total_revenue", "Total Revenue")}</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-2xl font-semibold">{data?.total_revenue ?? "—"}</p>
          </CardContent>
        </Card>

        {data?.by_method && Object.entries(data.by_method).length > 0 && (
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">{t("dashboard.payment_analytics.by_method", "By Method")}</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              {Object.entries(data.by_method).map(([method, amount]) => (
                <div key={method} className="flex justify-between text-sm">
                  <span className="text-muted-foreground capitalize">{method}</span>
                  <span className="font-semibold">{amount}</span>
                </div>
              ))}
            </CardContent>
          </Card>
        )}

        {data?.by_status && Object.entries(data.by_status).length > 0 && (
          <Card>
            <CardHeader>
              <CardTitle className="text-sm">{t("dashboard.payment_analytics.by_status", "By Status")}</CardTitle>
            </CardHeader>
            <CardContent className="space-y-2">
              {Object.entries(data.by_status).map(([status, count]) => (
                <div key={status} className="flex justify-between text-sm">
                  <span className="text-muted-foreground capitalize">{status}</span>
                  <span className="font-semibold">{count}</span>
                </div>
              ))}
            </CardContent>
          </Card>
        )}
      </div>

      {data?.monthly_revenue && data.monthly_revenue.length > 0 && (
        <Card>
          <CardHeader>
            <CardTitle className="text-sm">{t("dashboard.payment_analytics.monthly", "Monthly Revenue")}</CardTitle>
            <CardDescription>{t("dashboard.payment_analytics.monthly_desc", "Revenue breakdown by month.")}</CardDescription>
          </CardHeader>
          <CardContent className="space-y-2">
            {data.monthly_revenue.map((item) => (
              <div key={item.month} className="flex justify-between border-b border-border/40 pb-1 text-sm last:border-0">
                <span className="text-muted-foreground">{item.month}</span>
                <span className="font-semibold">{item.revenue}</span>
              </div>
            ))}
          </CardContent>
        </Card>
      )}
    </div>
  );
}
