"use client";

import { useEffect, useState } from "react";
import { Loader2, BarChart3 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { getEmDashboardStats } from "@/lib/api-email-marketing";
import type { EmDashboardStats } from "@/lib/api-email-marketing";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export default function EmailAnalyticsPage() {
  const [stats, setStats] = useState<EmDashboardStats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getEmDashboardStats()
      .then((r) => setStats(r.data))
      .catch(() => toast.error("Failed to load analytics"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;
  }

  const cards = [
    { label: "Total Campaigns", value: stats?.total_campaigns ?? 0 },
    { label: "Draft Campaigns", value: stats?.draft_campaigns ?? 0 },
    { label: "Sent Campaigns", value: stats?.sent_campaigns ?? 0 },
    { label: "Scheduled Campaigns", value: stats?.scheduled_campaigns ?? 0 },
    { label: "Total Contacts", value: stats?.total_contacts ?? 0 },
    { label: "Unsubscribed Contacts", value: stats?.unsubscribed_contacts ?? 0 },
    { label: "Open Rate", value: stats?.open_rate ?? "—" },
    { label: "Click Rate", value: stats?.click_rate ?? "—" },
    { label: "Bounce Rate", value: stats?.bounce_rate ?? "—" },
  ];

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={BarChart3}
        titleKey="email_marketing.analytics"
        titleFallback="Analytics"
        subtitleKey="email_marketing.analytics_subtitle"
        subtitleFallback="Email marketing performance insights"
        dashboardHref="/dashboard/modules/email-marketing"
        moduleKey="email_marketing"
      />
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {cards.map((c) => (
          <Card key={c.label}>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm font-medium">{c.label}</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{c.value}</div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
