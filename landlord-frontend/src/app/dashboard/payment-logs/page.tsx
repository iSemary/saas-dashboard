"use client";

import { useEffect, useState } from "react";
import { Loader2 } from "lucide-react";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

type PaymentLog = {
  id: number;
  tenant_id?: number;
  payment_method_id?: number;
  amount: number;
  currency: string;
  status: string;
  transaction_id?: string;
  created_at: string;
  metadata?: Record<string, unknown>;
};

export default function PaymentLogsPage() {
  const { t } = useI18n();
  const [logs, setLogs] = useState<PaymentLog[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get("/payment-logs")
      .then((res) => setLogs(res.data as PaymentLog[]))
      .catch(() => setLogs([]))
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
        <h1 className="text-xl font-semibold">{t("dashboard.payment_logs.title", "Payment Logs")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.payment_logs.subtitle", "History of all payment transactions.")}
        </p>
      </div>

      {logs.length === 0 ? (
        <Card>
          <CardContent className="flex min-h-[200px] items-center justify-center">
            <p className="text-muted-foreground">{t("dashboard.payment_logs.no_logs", "No payment logs found.")}</p>
          </CardContent>
        </Card>
      ) : (
        <div className="space-y-2">
          {logs.map((log) => (
            <Card key={log.id}>
              <CardHeader className="pb-3">
                <div className="flex items-center justify-between">
                  <CardTitle className="text-sm font-medium">
                    {log.transaction_id || `#${log.id}`}
                  </CardTitle>
                  <span className="text-xs font-medium px-2 py-1 rounded-full bg-muted">
                    {log.status}
                  </span>
                </div>
                <CardDescription className="text-xs">
                  {new Date(log.created_at).toLocaleString()}
                </CardDescription>
              </CardHeader>
              <CardContent>
                <div className="flex justify-between text-sm">
                  <span className="text-muted-foreground">{t("dashboard.payment_logs.amount", "Amount")}</span>
                  <span className="font-semibold">
                    {log.amount} {log.currency}
                  </span>
                </div>
                {log.tenant_id && (
                  <div className="flex justify-between text-sm mt-1">
                    <span className="text-muted-foreground">{t("dashboard.payment_logs.tenant", "Tenant ID")}</span>
                    <span>{log.tenant_id}</span>
                  </div>
                )}
                {log.payment_method_id && (
                  <div className="flex justify-between text-sm mt-1">
                    <span className="text-muted-foreground">{t("dashboard.payment_logs.method", "Payment Method")}</span>
                    <span>{log.payment_method_id}</span>
                  </div>
                )}
              </CardContent>
            </Card>
          ))}
        </div>
      )}
    </div>
  );
}
