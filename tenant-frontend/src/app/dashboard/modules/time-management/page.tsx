"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, Clock } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getTimeManagementData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_time_management";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["time_entries", "active_timers", "pending_timesheets", "todays_events"];
  const lg = keys.map((key, i) => ({
    i: key, x: i * 3, y: 0, w: 3, h: 2, minH: 2, minW: 2,
  }));
  const md = keys.map((key, i) => ({
    i: key, x: i * 3, y: 0, w: 3, h: 2, minH: 2, minW: 2,
  }));
  const sm = keys.map((key, i) => ({
    i: key, x: (i % 2) * 6, y: Math.floor(i / 2) * 3, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const xs = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

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

  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  const cards = [
    { key: "time_entries", label: t("dashboard.tm.time_entries", "Time Entries"), value: data?.time_entries_count ?? 0 },
    { key: "active_timers", label: t("dashboard.tm.active_timers", "Active Timers"), value: data?.active_timers_count ?? 0 },
    { key: "pending_timesheets", label: t("dashboard.tm.pending_timesheets", "Pending Timesheets"), value: data?.pending_timesheets_count ?? 0 },
    { key: "todays_events", label: t("dashboard.tm.todays_events", "Today's Events"), value: data?.calendar_events_today_count ?? 0 },
  ];

  const statCards = cards.map((c) => (
    <div key={c.key} className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium">{c.label}</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{c.value}</div>
        </CardContent>
      </Card>
    </div>
  ));

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
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {statCards}
      </DraggableDashboardGrid>
    </div>
  );
}
