"use client";

import { useEffect, useState } from "react";
import { Loader2, Megaphone, Users, FileText, Mail, TrendingUp, UserX } from "lucide-react";
import { getEmDashboardStats, type EmDashboardStats } from "@/lib/api-email-marketing";

export default function EmailMarketingDashboardPage() {
  const [stats, setStats] = useState<EmDashboardStats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getEmDashboardStats()
      .then(res => setStats(res.data))
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
    { label: "Total Campaigns", value: stats.total_campaigns, icon: Megaphone, color: "text-teal-600" },
    { label: "Active Contacts", value: stats.total_contacts, icon: Users, color: "text-blue-600" },
    { label: "Templates", value: stats.total_templates, icon: FileText, color: "text-purple-600" },
    { label: "Draft Campaigns", value: stats.draft_campaigns, icon: FileText, color: "text-yellow-600" },
    { label: "Sent Campaigns", value: stats.sent_campaigns, icon: Mail, color: "text-green-600" },
    { label: "Open Rate", value: `${stats.open_rate}%`, icon: TrendingUp, color: "text-emerald-600" },
    { label: "Click Rate", value: `${stats.click_rate}%`, icon: TrendingUp, color: "text-indigo-600" },
    { label: "Bounce Rate", value: `${stats.bounce_rate}%`, icon: TrendingUp, color: "text-red-600" },
    { label: "Unsubscribed", value: stats.unsubscribed_contacts, icon: UserX, color: "text-orange-600" },
    { label: "Contact Lists", value: stats.total_contact_lists, icon: Users, color: "text-cyan-600" },
    { label: "Scheduled", value: stats.scheduled_campaigns, icon: Mail, color: "text-amber-600" },
  ];

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-2xl font-bold">Email Marketing Dashboard</h1>
        <p className="text-muted-foreground">Campaign performance at a glance</p>
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
