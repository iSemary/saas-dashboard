"use client";

import { useEffect, useState } from "react";
import { Loader2, ShoppingBag } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { getSalesSummary } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type SalesSummary = {
  date?: string;
  total_orders?: number;
  total_revenue?: number;
  total_paid?: number;
  by_method?: Record<string, { count: number; revenue: number }>;
};

export default function SalesPage() {
  const [data, setData] = useState<SalesSummary | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getSalesSummary()
      .then((d) => setData(d as SalesSummary))
      .catch(() => toast.error("Failed to load sales summary"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { label: "Total Orders", value: data?.total_orders ?? 0 },
    { label: "Revenue Today", value: `$${(data?.total_revenue ?? 0).toLocaleString()}` },
    { label: "Amount Collected", value: `$${(data?.total_paid ?? 0).toLocaleString()}` },
  ];

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={ShoppingBag}
        titleKey="dashboard.sales.title"
        titleFallback="Sales"
        subtitleKey="dashboard.sales.subtitle"
        subtitleFallback="Daily sales overview"
        dashboardHref="/dashboard/modules/sales"
        moduleKey="sales"
      />
      <div className="grid gap-4 sm:grid-cols-3">
        {cards.map((c) => (
          <Card key={c.label}>
            <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">{c.label}</CardTitle></CardHeader>
            <CardContent><div className="text-2xl font-bold">{c.value}</div></CardContent>
          </Card>
        ))}
      </div>
      {data?.by_method && (
        <Card>
          <CardHeader><CardTitle className="text-sm font-medium">Revenue by Payment Method</CardTitle></CardHeader>
          <CardContent>
            <div className="grid gap-2 sm:grid-cols-2 md:grid-cols-4">
              {Object.entries(data.by_method).map(([method, stats]) => (
                <div key={method} className="rounded-lg border p-3">
                  <p className="text-xs text-muted-foreground capitalize">{method}</p>
                  <p className="text-lg font-bold">${stats.revenue.toLocaleString()}</p>
                  <p className="text-xs text-muted-foreground">{stats.count} orders</p>
                </div>
              ))}
            </div>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
