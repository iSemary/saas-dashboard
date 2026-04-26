"use client";

import { useEffect, useState } from "react";
import { Loader2, BarChart3 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { getSmDashboardStats } from "@/lib/api-sms-marketing";
import type { SmDashboardStats } from "@/lib/api-sms-marketing";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export default function SmsAnalyticsPage() {
  const [stats, setStats] = useState<SmDashboardStats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getSmDashboardStats()
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
    { label: "Opted Out Contacts", value: stats?.opted_out_contacts ?? 0 },
    { label: "Delivery Rate", value: stats?.delivery_rate ?? "—" },
    { label: "Failure Rate", value: stats?.failure_rate ?? "—" },
    { label: "Total Cost", value: stats?.total_cost ?? 0 },
  ];

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={BarChart3}
        titleKey="sms_marketing.analytics"
        titleFallback="Analytics"
        subtitleKey="sms_marketing.analytics_subtitle"
        subtitleFallback="SMS marketing performance insights"
        dashboardHref="/dashboard/modules/sms-marketing"
        moduleKey="sms_marketing"
      />
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
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
