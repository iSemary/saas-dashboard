"use client";

import { useEffect, useState } from "react";
import { Loader2, ShoppingCart } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getPosData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ApexChart } from "@/components/ui/apex-chart";

type DailySale = { date: string; orders: number; revenue: number };
type OrderStatus = { status: string; count: number };
type RevenueCategory = { category: string; product_count: number; stock_value: number };

type PosData = {
  products_count?: number;
  orders_count?: number;
  revenue_today?: number;
  daily_sales?: DailySale[];
  orders_by_status?: OrderStatus[];
  revenue_by_category?: RevenueCategory[];
};

export default function PosPage() {
  const { t } = useI18n();
  const [data, setData] = useState<PosData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getPosData().then((d) => setData(d as PosData)).catch(() => toast.error("Failed to load POS data")).finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { label: t("dashboard.pos.products", "Products"), value: data?.products_count ?? 0 },
    { label: t("dashboard.pos.orders", "Orders"), value: data?.orders_count ?? 0 },
    { label: t("dashboard.pos.revenue_today", "Revenue Today"), value: `$${(data?.revenue_today ?? 0).toLocaleString()}` },
  ];

  const salesSeries = [
    { name: t("dashboard.pos.revenue", "Revenue"), data: (data?.daily_sales ?? []).map((s) => s.revenue) },
    { name: t("dashboard.pos.orders", "Orders"), data: (data?.daily_sales ?? []).map((s) => s.orders) },
  ];
  const salesOptions = {
    chart: { id: "pos-daily-sales" },
    xaxis: { categories: (data?.daily_sales ?? []).map((s) => s.date) },
    title: { text: t("dashboard.pos.sales_trend_title", "Daily Sales Trend (Last 14 Days)"), align: "left" as const },
    yaxis: [
      { title: { text: t("dashboard.pos.revenue", "Revenue ($)") }, opposite: true },
      { title: { text: t("dashboard.pos.orders", "Orders") } },
    ],
  };

  const statusSeries = (data?.orders_by_status ?? []).map((s) => ({ x: s.status, y: s.count }));
  const statusOptions = {
    chart: { id: "pos-order-status" },
    labels: (data?.orders_by_status ?? []).map((s) => s.status),
    title: { text: t("dashboard.pos.order_status_title", "Orders by Status"), align: "left" as const },
    legend: { position: "bottom" as const },
  };

  const categorySeries = [{
    name: t("dashboard.pos.stock_value", "Stock Value"),
    data: (data?.revenue_by_category ?? []).map((c) => c.stock_value),
  }];
  const categoryOptions = {
    chart: { id: "pos-category-revenue" },
    xaxis: { categories: (data?.revenue_by_category ?? []).map((c) => c.category) },
    plotOptions: { bar: { borderRadius: 4 } },
    title: { text: t("dashboard.pos.category_title", "Stock Value by Category"), align: "left" as const },
    yaxis: {
      labels: {
        formatter: (value: number) => `$${value?.toLocaleString() ?? "0"}`,
      },
    },
  };

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={ShoppingCart}
        titleKey="dashboard.pos.title"
        titleFallback="POS Module"
        subtitleKey="dashboard.pos.subtitle"
        subtitleFallback="Point of sale overview"
        dashboardHref="/dashboard/modules/pos"
        moduleKey="pos"
      />
      <div className="grid gap-4 sm:grid-cols-3">
        {cards.map((c) => (
          <Card key={c.label}>
            <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">{c.label}</CardTitle></CardHeader>
            <CardContent><div className="text-2xl font-bold">{c.value}</div></CardContent>
          </Card>
        ))}
      </div>
      <Card>
        <CardContent className="pt-4">
          <ApexChart type="area" series={salesSeries} options={salesOptions} height={300} />
        </CardContent>
      </Card>
      <div className="grid gap-4 md:grid-cols-2">
        <Card>
          <CardContent className="pt-4">
            <ApexChart type="donut" series={statusSeries} options={statusOptions} height={300} />
          </CardContent>
        </Card>
        <Card>
          <CardContent className="pt-4">
            <ApexChart type="bar" series={categorySeries} options={categoryOptions} height={300} />
          </CardContent>
        </Card>
      </div>
    </div>
  );
}
