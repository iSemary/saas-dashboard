"use client";

import { useEffect, useState } from "react";
import { Loader2, BarChart3 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { getTmReportUtilization, getTmReportSubmittedHours, getTmReportAnomalies, getTmReportOvertime, getTmReportBillableRatio } from "@/lib/tm-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

export default function ReportsPage() {
  const [cards, setCards] = useState<{ label: string; value: number | string }[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([getTmReportUtilization(), getTmReportSubmittedHours(), getTmReportAnomalies(), getTmReportOvertime(), getTmReportBillableRatio()])
      .then(([util, hours, anomalies, ot, billable]) =>
        setCards([
          { label: "Utilization Rate", value: util?.utilization_rate ?? "—" },
          { label: "Submitted Hours", value: hours?.total_hours ?? 0 },
          { label: "Anomalies", value: anomalies?.anomaly_count ?? 0 },
          { label: "Overtime Hours", value: ot?.overtime_hours ?? 0 },
          { label: "Billable Ratio", value: billable?.billable_ratio ?? "—" },
        ])
      )
      .catch(() => toast.error("Failed to load reports"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;
  }

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={BarChart3}
        titleKey="tm.reports"
        titleFallback="Reports"
        subtitleKey="tm.reports_subtitle"
        subtitleFallback="Time management analytics and insights"
        dashboardHref="/dashboard/modules/time-management"
        moduleKey="time_management"
      />
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5">
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
