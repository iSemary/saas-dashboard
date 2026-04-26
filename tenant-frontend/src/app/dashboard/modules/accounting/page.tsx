"use client";

import { useEffect, useState } from "react";
import { Loader2, TrendingUp, BookOpen, DollarSign, Calendar, Landmark } from "lucide-react";
import { getAccountingDashboard } from "@/lib/accounting-resources";

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
    { label: "Active Accounts", value: stats.active_accounts, icon: BookOpen, color: "text-blue-600" },
    { label: "Draft Entries", value: stats.draft_entries, icon: TrendingUp, color: "text-yellow-600" },
    { label: "Posted Entries", value: stats.posted_entries, icon: TrendingUp, color: "text-green-600" },
    { label: "Total Debit", value: `$${stats.total_debit.toLocaleString()}`, icon: DollarSign, color: "text-emerald-600" },
    { label: "Total Credit", value: `$${stats.total_credit.toLocaleString()}`, icon: DollarSign, color: "text-red-600" },
    { label: "Active Fiscal Years", value: stats.active_fiscal_years, icon: Calendar, color: "text-purple-600" },
    { label: "Active Budgets", value: stats.active_budgets, icon: Landmark, color: "text-indigo-600" },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Accounting Dashboard</h1>
        <p className="text-muted-foreground">Financial overview at a glance</p>
      </div>
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {cards.map((card) => (
          <div key={card.label} className="rounded-xl border bg-card p-4 shadow-sm">
            <div className="flex items-center gap-3">
              <card.icon className={`size-8 ${card.color}`} />
              <div>
                <p className="text-sm text-muted-foreground">{card.label}</p>
                <p className="text-xl font-semibold">{card.value}</p>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
