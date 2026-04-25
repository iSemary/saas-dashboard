"use client";

import { useEffect, useState } from "react";
import { BarChart3 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { apiClient } from "@/lib/api-client";

export default function HrAttendanceReportPage() {
  const [data, setData] = useState<unknown>(null);

  useEffect(() => {
    apiClient.get("/tenant/hr/reports/attendance-summary").then((res) => setData(res.data)).catch(() => setData(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={BarChart3}
        titleKey="dashboard.hr.report_attendance"
        titleFallback="Attendance Summary"
        subtitleKey="dashboard.hr.report_attendance_subtitle"
        subtitleFallback="Attendance distribution by status"
        dashboardHref="/dashboard/modules/hr/reports"
        moduleKey="hr"
      />
      <Card><CardContent className="pt-4 text-sm">{data ? <pre className="text-xs overflow-auto">{JSON.stringify(data, null, 2)}</pre> : "No report data."}</CardContent></Card>
    </div>
  );
}
