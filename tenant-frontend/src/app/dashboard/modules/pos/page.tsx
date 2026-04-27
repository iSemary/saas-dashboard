"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, ShoppingCart } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getPosData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ApexChart } from "@/components/ui/apex-chart";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_pos";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["products", "orders", "revenue_today", "sales_trend", "order_status", "category_revenue"];
  const lg = keys.map((key, i) => {
    const isChart = ["sales_trend", "order_status", "category_revenue"].includes(key);
    const row = i < 3 ? 0 : (key === "sales_trend" ? 2 : 6);
    const w = key === "sales_trend" ? 12 : 6;
    return { i: key, x: key === "sales_trend" ? 0 : (i % 2) * 6, y: row, w, h: isChart ? 4 : 2, minH: 2, minW: 2 };
  });
  const md = keys.map((key, i) => {
    const isChart = ["sales_trend", "order_status", "category_revenue"].includes(key);
    const row = i < 3 ? 0 : (key === "sales_trend" ? 2 : 6);
    const w = key === "sales_trend" ? 12 : 6;
    return { i: key, x: key === "sales_trend" ? 0 : (i % 2) * 6, y: row, w, h: isChart ? 4 : 2, minH: 2, minW: 2 };
  });
  const sm = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const xs = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

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

  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { key: "products", label: t("dashboard.pos.products", "Products"), value: data?.products_count ?? 0 },
    { key: "orders", label: t("dashboard.pos.orders", "Orders"), value: data?.orders_count ?? 0 },
    { key: "revenue_today", label: t("dashboard.pos.revenue_today", "Revenue Today"), value: `$${(data?.revenue_today ?? 0).toLocaleString()}` },
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

  const statCards = cards.map((c) => (
    <div key={c.key} className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">{c.label}</CardTitle></CardHeader>
        <CardContent><div className="text-2xl font-bold">{c.value}</div></CardContent>
      </Card>
    </div>
  ));

  const chartCards = [
    <div key="sales_trend" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="area" series={salesSeries} options={salesOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
    <div key="order_status" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="donut" series={statusSeries} options={statusOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
    <div key="category_revenue" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="bar" series={categorySeries} options={categoryOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
  ];

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
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {[...statCards, ...chartCards]}
      </DraggableDashboardGrid>
    </div>
  );
}
