"use client";

import { useEffect, useState } from "react";
import { BarChart3 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { apiClient } from "@/lib/api-client";

export default function HrLeaveReportPage() {
  const [data, setData] = useState<unknown>(null);

  useEffect(() => {
    apiClient.get("/tenant/hr/reports/leave-usage").then((res) => setData(res.data)).catch(() => setData(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={BarChart3}
        titleKey="dashboard.hr.report_leave"
        titleFallback="Leave Usage"
        subtitleKey="dashboard.hr.report_leave_subtitle"
        subtitleFallback="Leave requests by status and type"
        dashboardHref="/dashboard/modules/hr/reports"
        moduleKey="hr"
      />
      <Card><CardContent className="pt-4 text-sm">{data ? <pre className="text-xs overflow-auto">{JSON.stringify(data, null, 2)}</pre> : "No report data."}</CardContent></Card>
    </div>
  );
}
