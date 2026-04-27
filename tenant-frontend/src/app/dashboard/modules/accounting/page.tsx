"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, TrendingUp, BookOpen, DollarSign, Calendar, Landmark } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { getAccountingDashboard } from "@/lib/accounting-resources";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_accounting";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["active_accounts", "draft_entries", "posted_entries", "total_debit", "total_credit", "fiscal_years", "budgets"];
  const lg = keys.map((key, i) => ({
    i: key, x: (i % 4) * 3, y: Math.floor(i / 4) * 2, w: 3, h: 2, minH: 2, minW: 2,
  }));
  const md = keys.map((key, i) => ({
    i: key, x: (i % 4) * 3, y: Math.floor(i / 4) * 2, w: 3, h: 2, minH: 2, minW: 2,
  }));
  const sm = keys.map((key, i) => ({
    i: key, x: (i % 2) * 6, y: Math.floor(i / 2) * 3, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const xs = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

type Stats = {
  active_accounts: number;
  draft_entries: number;
  posted_entries: number;
  active_fiscal_years: number;
  active_budgets: number;
  total_debit: number;
  total_credit: number;
};

export default function AccountingDashboardPage() {
  const [stats, setStats] = useState<Stats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getAccountingDashboard()
      .then(setStats)
      .catch(() => setStats(null))
      .finally(() => setLoading(false));
  }, []);

  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  if (!stats) {
    return <p className="text-muted-foreground">Failed to load dashboard.</p>;
  }

  const cards = [
    { key: "active_accounts", label: "Active Accounts", value: stats.active_accounts, icon: BookOpen, color: "text-blue-600" },
    { key: "draft_entries", label: "Draft Entries", value: stats.draft_entries, icon: TrendingUp, color: "text-yellow-600" },
    { key: "posted_entries", label: "Posted Entries", value: stats.posted_entries, icon: TrendingUp, color: "text-green-600" },
    { key: "total_debit", label: "Total Debit", value: `$${stats.total_debit.toLocaleString()}`, icon: DollarSign, color: "text-emerald-600" },
    { key: "total_credit", label: "Total Credit", value: `$${stats.total_credit.toLocaleString()}`, icon: DollarSign, color: "text-red-600" },
    { key: "fiscal_years", label: "Active Fiscal Years", value: stats.active_fiscal_years, icon: Calendar, color: "text-purple-600" },
    { key: "budgets", label: "Active Budgets", value: stats.active_budgets, icon: Landmark, color: "text-indigo-600" },
  ];

  const statCards = cards.map((card) => (
    <div key={card.key} className="h-full">
      <div className="h-full rounded-xl border bg-card p-4 shadow-sm">
        <div className="flex items-center gap-3">
          <card.icon className={`size-8 ${card.color}`} />
          <div>
            <p className="text-sm text-muted-foreground">{card.label}</p>
            <p className="text-xl font-semibold">{card.value}</p>
          </div>
        </div>
      </div>
    </div>
  ));

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Accounting Dashboard</h1>
        <p className="text-muted-foreground">Financial overview at a glance</p>
      </div>
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {statCards}
      </DraggableDashboardGrid>
    </div>
  );
}
