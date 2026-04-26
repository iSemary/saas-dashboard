"use client";

import { useEffect, useState } from "react";
import { Loader2, Receipt, CheckCircle, XCircle, DollarSign, FileText, Clock } from "lucide-react";
import { getExpensesDashboard } from "@/lib/expenses-resources";

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
    { label: "Total Expenses", value: `$${stats.total_expenses.toLocaleString()}`, icon: DollarSign, color: "text-blue-600" },
    { label: "Pending", value: stats.pending_count, icon: Clock, color: "text-yellow-600" },
    { label: "Approved", value: stats.approved_count, icon: CheckCircle, color: "text-green-600" },
    { label: "Rejected", value: stats.rejected_count, icon: XCircle, color: "text-red-600" },
    { label: "Reimbursed Total", value: `$${stats.reimbursed_total.toLocaleString()}`, icon: Receipt, color: "text-emerald-600" },
    { label: "Pending Reports", value: stats.pending_reports, icon: FileText, color: "text-purple-600" },
    { label: "Pending Reimbursements", value: `$${stats.pending_reimbursements.toLocaleString()}`, icon: DollarSign, color: "text-indigo-600" },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Expenses Dashboard</h1>
        <p className="text-muted-foreground">Expense management overview</p>
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
