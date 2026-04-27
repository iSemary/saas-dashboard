"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, Receipt, CheckCircle, XCircle, DollarSign, FileText, Clock } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { getExpensesDashboard } from "@/lib/expenses-resources";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_expenses";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["total", "pending", "approved", "rejected", "reimbursed", "pending_reports", "pending_reimbursements"];
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
  total_expenses: number;
  pending_count: number;
  approved_count: number;
  rejected_count: number;
  reimbursed_total: number;
  pending_reports: number;
  pending_reimbursements: number;
};

export default function ExpensesDashboardPage() {
  const [stats, setStats] = useState<Stats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getExpensesDashboard()
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
    { key: "total", label: "Total Expenses", value: `$${stats.total_expenses.toLocaleString()}`, icon: DollarSign, color: "text-blue-600" },
    { key: "pending", label: "Pending", value: stats.pending_count, icon: Clock, color: "text-yellow-600" },
    { key: "approved", label: "Approved", value: stats.approved_count, icon: CheckCircle, color: "text-green-600" },
    { key: "rejected", label: "Rejected", value: stats.rejected_count, icon: XCircle, color: "text-red-600" },
    { key: "reimbursed", label: "Reimbursed Total", value: `$${stats.reimbursed_total.toLocaleString()}`, icon: Receipt, color: "text-emerald-600" },
    { key: "pending_reports", label: "Pending Reports", value: stats.pending_reports, icon: FileText, color: "text-purple-600" },
    { key: "pending_reimbursements", label: "Pending Reimbursements", value: `$${stats.pending_reimbursements.toLocaleString()}`, icon: DollarSign, color: "text-indigo-600" },
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
        <h1 className="text-2xl font-bold">Expenses Dashboard</h1>
        <p className="text-muted-foreground">Expense management overview</p>
      </div>
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {statCards}
      </DraggableDashboardGrid>
    </div>
  );
}
