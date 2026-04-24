"use client";

import { useEffect, useMemo, useState } from "react";
import Link from "next/link";
import {
  Area,
  AreaChart,
  Bar,
  BarChart,
  CartesianGrid,
  Line,
  LineChart,
  ResponsiveContainer,
  Tooltip,
  XAxis,
  YAxis,
} from "recharts";
import { Users, Building2, Tag, Store, Puzzle } from "lucide-react";
import { useChartPalette } from "@/context/chart-palette-context";
import { useI18n } from "@/context/i18n-context";
import {
  landlordDashboardStats,
  landlordUserChart,
  landlordTenantChart,
  landlordEmailChart,
  landlordModuleStats,
  type LandlordDashboardStats,
  type ChartDataPoint,
  type ModuleStats,
} from "@/lib/resources";

export default function DashboardPage() {
  const { t } = useI18n();
  const { palette } = useChartPalette();
  const chartColor = (i: number) => palette[i % palette.length]!;

  const [stats, setStats] = useState<LandlordDashboardStats | null>(null);
  const [userChartData, setUserChartData] = useState<ChartDataPoint[]>([]);
  const [tenantChartData, setTenantChartData] = useState<ChartDataPoint[]>([]);
  const [emailChartData, setEmailChartData] = useState<ChartDataPoint[]>([]);
  const [moduleStatsData, setModuleStatsData] = useState<ModuleStats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      landlordDashboardStats().catch(() => null),
      landlordUserChart().catch(() => []),
      landlordTenantChart().catch(() => []),
      landlordEmailChart().catch(() => []),
      landlordModuleStats().catch(() => null),
    ]).then(([s, u, te, e, m]) => {
      setStats(s);
      setUserChartData(u);
      setTenantChartData(te);
      setEmailChartData(e);
      setModuleStatsData(m);
      setLoading(false);
    });
  }, []);

  const formatXAxis = (v: string) => {
    try {
      return new Date(v).toLocaleDateString(undefined, { month: "short", day: "numeric" });
    } catch {
      return v;
    }
  };

  const statCards = useMemo(() => {
    if (!stats) return [];
    return [
      {
        key: "users",
        label: t("dashboard.home.stat_users", "Total Users"),
        value: stats.users.total,
        change: stats.users.growth_rate,
        icon: Users,
        href: "/dashboard/system-users",
      },
      {
        key: "tenants",
        label: t("dashboard.home.stat_tenants", "Total Tenants"),
        value: stats.tenants.total,
        change: stats.tenants.growth_rate,
        icon: Building2,
        href: "/dashboard/tenants",
      },
      {
        key: "categories",
        label: t("dashboard.home.stat_categories", "Categories"),
        value: stats.categories.total,
        change: null,
        extra: t("dashboard.home.active", `${stats.categories.active} active`),
        icon: Tag,
        href: "/dashboard/categories",
      },
      {
        key: "brands",
        label: t("dashboard.home.stat_brands", "Brands"),
        value: stats.brands.total,
        change: stats.brands.growth_rate,
        icon: Store,
        href: "/dashboard/brands",
      },
      {
        key: "brand_modules",
        label: t("dashboard.home.stat_active_modules", "Active Module Subscriptions"),
        value: stats.brand_modules.active_subscriptions,
        change: null,
        extra: t("dashboard.home.brands_with_modules", `${stats.brand_modules.brands_with_modules} brands with modules`),
        icon: Puzzle,
        href: "/dashboard/modules",
      },
    ];
  }, [stats, t]);

  if (loading) {
    return (
      <div className="flex items-center justify-center py-20">
        <div className="size-8 animate-spin rounded-full border-4 border-primary border-t-transparent" />
      </div>
    );
  }

  return (
    <div className="space-y-5">
      <div className="rounded-xl border bg-muted/40 p-5">
        <h1 className="text-2xl font-semibold">{t("dashboard.home.title", "Dashboard")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.home.subtitle_landlord", "Overview of users, tenants, and platform activity.")}
        </p>
      </div>

      {/* Stats Cards */}
      <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
        {statCards.map((card) => (
          <Link
            key={card.key}
            href={card.href}
            className="rounded-xl border bg-card p-4 shadow-sm transition hover:shadow-md hover:-translate-y-0.5"
          >
            <div className="flex items-center justify-between">
              <p className="text-xs uppercase tracking-wide text-muted-foreground">{card.label}</p>
              <card.icon className="size-4 text-muted-foreground" />
            </div>
            <p className="mt-2 text-2xl font-semibold">{card.value}</p>
            {card.change !== null ? (
              <p className={`mt-1 text-xs ${card.change >= 0 ? "text-green-600" : "text-red-600"}`}>
                {card.change >= 0 ? "↑" : "↓"} {Math.abs(card.change)}% {t("dashboard.home.this_month", "this month")}
              </p>
            ) : card.extra ? (
              <p className="mt-1 text-xs text-muted-foreground">{card.extra}</p>
            ) : null}
          </Link>
        ))}
      </div>

      {/* Charts Row 1: User Growth + Tenant Growth */}
      <div className="grid gap-4 lg:grid-cols-2">
        <div className="rounded-xl border bg-card p-4 shadow-sm" dir="ltr">
          <h2 className="mb-3 text-sm font-semibold">{t("dashboard.home.chart_user_growth", "User Growth (Last 30 Days)")}</h2>
          <div className="h-64 w-full min-w-0">
            <ResponsiveContainer width="100%" height="100%">
              <LineChart data={userChartData} margin={{ top: 8, right: 8, left: 0, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                <XAxis dataKey="date" tick={{ fontSize: 10 }} tickFormatter={formatXAxis} />
                <YAxis allowDecimals={false} width={32} tick={{ fontSize: 10 }} />
                <Tooltip contentStyle={{ borderRadius: 8 }} labelFormatter={(v) => String(v)} />
                <Line type="monotone" dataKey="count" stroke={chartColor(0)} strokeWidth={2} dot={false} name={t("dashboard.home.new_users", "New Users")} />
              </LineChart>
            </ResponsiveContainer>
          </div>
        </div>

        <div className="rounded-xl border bg-card p-4 shadow-sm" dir="ltr">
          <h2 className="mb-3 text-sm font-semibold">{t("dashboard.home.chart_tenant_growth", "Tenant Growth (Last 30 Days)")}</h2>
          <div className="h-64 w-full min-w-0">
            <ResponsiveContainer width="100%" height="100%">
              <BarChart data={tenantChartData} margin={{ top: 8, right: 8, left: 0, bottom: 0 }}>
                <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
                <XAxis dataKey="date" tick={{ fontSize: 10 }} tickFormatter={formatXAxis} />
                <YAxis allowDecimals={false} width={32} tick={{ fontSize: 10 }} />
                <Tooltip contentStyle={{ borderRadius: 8 }} labelFormatter={(v) => String(v)} />
                <Bar dataKey="count" fill={chartColor(1)} radius={[4, 4, 0, 0]} name={t("dashboard.home.new_tenants", "New Tenants")} />
              </BarChart>
            </ResponsiveContainer>
          </div>
        </div>
      </div>

      {/* Email Activity Chart */}
      <div className="rounded-xl border bg-card p-4 shadow-sm" dir="ltr">
        <h2 className="mb-3 text-sm font-semibold">{t("dashboard.home.chart_email_activity", "Email Activity (Last 30 Days)")}</h2>
        <div className="h-48 w-full min-w-0">
          <ResponsiveContainer width="100%" height="100%">
            <AreaChart data={emailChartData} margin={{ top: 8, right: 8, left: 0, bottom: 0 }}>
              <CartesianGrid strokeDasharray="3 3" className="stroke-muted" />
              <XAxis dataKey="date" tick={{ fontSize: 10 }} tickFormatter={formatXAxis} />
              <YAxis allowDecimals={false} width={32} tick={{ fontSize: 10 }} />
              <Tooltip contentStyle={{ borderRadius: 8 }} labelFormatter={(v) => String(v)} />
              <Area
                type="monotone"
                dataKey="count"
                stroke={chartColor(2)}
                fill={chartColor(2)}
                fillOpacity={0.2}
                strokeWidth={2}
                name={t("dashboard.home.emails_sent", "Emails Sent")}
              />
            </AreaChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Module Statistics */}
      {moduleStatsData && Object.keys(moduleStatsData).length > 0 && (
        <div className="space-y-3">
          <h2 className="text-sm font-semibold">{t("dashboard.home.module_statistics", "Module Statistics")}</h2>
          <div className="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            {Object.entries(moduleStatsData).map(([moduleName, moduleData]) => (
              <div key={moduleName} className="rounded-xl border bg-card p-4 shadow-sm">
                <h3 className="mb-3 text-sm font-semibold">{moduleName}</h3>
                <div className="space-y-2">
                  {Object.entries(moduleData).map(([statName, statValue]) => (
                    <div key={statName} className="flex items-center justify-between border-b border-border/40 pb-1 last:border-0 last:pb-0">
                      <span className="text-xs text-muted-foreground capitalize">{statName.replace(/_/g, " ")}</span>
                      <span className="text-xs font-semibold">{statValue}</span>
                    </div>
                  ))}
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
