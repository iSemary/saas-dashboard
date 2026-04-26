"use client";

import { useEffect, useState } from "react";
import { Loader2, Clock } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getTimeManagementData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type TmData = {
  time_entries_count?: number;
  active_timers_count?: number;
  pending_timesheets_count?: number;
  calendar_events_today_count?: number;
};

export default function TimeManagementPage() {
  const { t } = useI18n();
  const [data, setData] = useState<TmData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getTimeManagementData()
      .then((d) => setData(d as TmData))
      .catch(() => toast.error("Failed to load Time Management data"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  const cards = [
    { label: t("dashboard.tm.time_entries", "Time Entries"), value: data?.time_entries_count ?? 0 },
    { label: t("dashboard.tm.active_timers", "Active Timers"), value: data?.active_timers_count ?? 0 },
    { label: t("dashboard.tm.pending_timesheets", "Pending Timesheets"), value: data?.pending_timesheets_count ?? 0 },
    { label: t("dashboard.tm.todays_events", "Today's Events"), value: data?.calendar_events_today_count ?? 0 },
  ];

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Clock}
        titleKey="dashboard.tm.title"
        titleFallback="Time Management"
        subtitleKey="dashboard.tm.subtitle"
        subtitleFallback="Track time, manage calendars, and monitor productivity"
        dashboardHref="/dashboard/modules/time-management"
        moduleKey="time_management"
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
