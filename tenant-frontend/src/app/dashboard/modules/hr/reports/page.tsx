"use client";

import { useEffect, useState } from "react";
import { BarChart3 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { getHrReportHeadcount } from "@/lib/api-hr";

type Headcount = { total?: number; active?: number; terminated?: number };

export default function HrReportsPage() {
  const [data, setData] = useState<Headcount>({});

  useEffect(() => {
    getHrReportHeadcount().then((res) => setData((res?.data ?? res) as Headcount)).catch(() => setData({}));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={BarChart3}
        titleKey="dashboard.hr.reports"
        titleFallback="HR Reports"
        subtitleKey="dashboard.hr.reports_subtitle"
        subtitleFallback="Headcount and operational HR analytics"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <div className="grid gap-4 sm:grid-cols-3">
        <Card><CardHeader><CardTitle>Total Employees</CardTitle></CardHeader><CardContent>{data.total ?? 0}</CardContent></Card>
        <Card><CardHeader><CardTitle>Active</CardTitle></CardHeader><CardContent>{data.active ?? 0}</CardContent></Card>
        <Card><CardHeader><CardTitle>Terminated</CardTitle></CardHeader><CardContent>{data.terminated ?? 0}</CardContent></Card>
      </div>
    </div>
  );
}
