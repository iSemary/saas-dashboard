"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, ShoppingBag } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { getSalesSummary } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_sales";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["total_orders", "revenue_today", "amount_collected", "payment_methods"];
  const lg = keys.map((key, i) => ({
    i: key, x: (i % 3) * 4, y: Math.floor(i / 3) * 3, w: 4, h: key === "payment_methods" ? 4 : 3, minH: 2, minW: 2,
  }));
  const md = keys.map((key, i) => ({
    i: key, x: (i % 3) * 4, y: Math.floor(i / 3) * 3, w: 4, h: key === "payment_methods" ? 4 : 3, minH: 2, minW: 2,
  }));
  const sm = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 6, h: key === "payment_methods" ? 4 : 3, minH: 2, minW: 2,
  }));
  const xs = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: key === "payment_methods" ? 4 : 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

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

  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { key: "total_orders", label: "Total Orders", value: data?.total_orders ?? 0 },
    { key: "revenue_today", label: "Revenue Today", value: `$${(data?.total_revenue ?? 0).toLocaleString()}` },
    { key: "amount_collected", label: "Amount Collected", value: `$${(data?.total_paid ?? 0).toLocaleString()}` },
  ];

  const statCards = cards.map((c) => (
    <div key={c.key} className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">{c.label}</CardTitle></CardHeader>
        <CardContent><div className="text-2xl font-bold">{c.value}</div></CardContent>
      </Card>
    </div>
  ));

  const paymentMethodCard = data?.by_method ? (
    <div key="payment_methods" className="h-full">
      <Card className="h-full">
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
    </div>
  ) : null;

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
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {[...statCards, ...(paymentMethodCard ? [paymentMethodCard] : [])]}
      </DraggableDashboardGrid>
    </div>
  );
}
