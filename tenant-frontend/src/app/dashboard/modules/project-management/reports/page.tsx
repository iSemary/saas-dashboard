"use client";

import { useEffect, useState } from "react";
import { Loader2, BarChart3 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { getPmReportThroughput, getPmReportOverdue, getPmReportWorkload, getPmReportHealth } from "@/lib/pm-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type ReportData = { throughput?: number; overdue_count?: number; workload_score?: number; health_score?: number };

export default function ReportsPage() {
  const [data, setData] = useState<ReportData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([getPmReportThroughput(), getPmReportOverdue(), getPmReportWorkload(), getPmReportHealth()])
      .then(([throughput, overdue, workload, health]) =>
        setData({
          throughput: throughput?.throughput ?? 0,
          overdue_count: overdue?.overdue_count ?? 0,
          workload_score: workload?.workload_score ?? 0,
          health_score: health?.health_score ?? 0,
        })
      )
      .catch(() => toast.error("Failed to load reports"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;
  }

  const cards = [
    { label: "Throughput", value: data?.throughput ?? 0 },
    { label: "Overdue Tasks", value: data?.overdue_count ?? 0 },
    { label: "Workload Score", value: data?.workload_score ?? 0 },
    { label: "Health Score", value: data?.health_score ?? 0 },
  ];

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={BarChart3}
        titleKey="pm.reports"
        titleFallback="Reports"
        subtitleKey="pm.reports_subtitle"
        subtitleFallback="Project analytics and insights"
        dashboardHref="/dashboard/modules/project-management"
        moduleKey="project_management"
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
