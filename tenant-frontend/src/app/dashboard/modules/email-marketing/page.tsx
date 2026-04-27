"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, Megaphone, Users, FileText, Mail, TrendingUp, UserX } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { getEmDashboardStats, type EmDashboardStats } from "@/lib/api-email-marketing";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_email_marketing";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["campaigns", "contacts", "templates", "draft", "sent", "open_rate", "click_rate", "bounce_rate", "unsubscribed", "lists", "scheduled"];
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

export default function EmailMarketingDashboardPage() {
  const [stats, setStats] = useState<EmDashboardStats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getEmDashboardStats()
      .then(res => setStats(res.data))
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
    { key: "campaigns", label: "Total Campaigns", value: stats.total_campaigns, icon: Megaphone, color: "text-teal-600" },
    { key: "contacts", label: "Active Contacts", value: stats.total_contacts, icon: Users, color: "text-blue-600" },
    { key: "templates", label: "Templates", value: stats.total_templates, icon: FileText, color: "text-purple-600" },
    { key: "draft", label: "Draft Campaigns", value: stats.draft_campaigns, icon: FileText, color: "text-yellow-600" },
    { key: "sent", label: "Sent Campaigns", value: stats.sent_campaigns, icon: Mail, color: "text-green-600" },
    { key: "open_rate", label: "Open Rate", value: `${stats.open_rate}%`, icon: TrendingUp, color: "text-emerald-600" },
    { key: "click_rate", label: "Click Rate", value: `${stats.click_rate}%`, icon: TrendingUp, color: "text-indigo-600" },
    { key: "bounce_rate", label: "Bounce Rate", value: `${stats.bounce_rate}%`, icon: TrendingUp, color: "text-red-600" },
    { key: "unsubscribed", label: "Unsubscribed", value: stats.unsubscribed_contacts, icon: UserX, color: "text-orange-600" },
    { key: "lists", label: "Contact Lists", value: stats.total_contact_lists, icon: Users, color: "text-cyan-600" },
    { key: "scheduled", label: "Scheduled", value: stats.scheduled_campaigns, icon: Mail, color: "text-amber-600" },
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
        <h1 className="text-2xl font-bold">Email Marketing Dashboard</h1>
        <p className="text-muted-foreground">Campaign performance at a glance</p>
      </div>
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {statCards}
      </DraggableDashboardGrid>
    </div>
  );
}
