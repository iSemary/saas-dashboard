"use client";

import { useEffect, useState } from "react";
import { BarChart3 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { apiClient } from "@/lib/api-client";

export default function HrRecruitmentReportPage() {
  const [data, setData] = useState<unknown>(null);

  useEffect(() => {
    apiClient.get("/tenant/hr/reports/recruitment-funnel").then((res) => setData(res.data)).catch(() => setData(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={BarChart3}
        titleKey="dashboard.hr.report_recruitment"
        titleFallback="Recruitment Funnel"
        subtitleKey="dashboard.hr.report_recruitment_subtitle"
        subtitleFallback="Candidates by application stage/status"
        dashboardHref="/dashboard/modules/hr/reports"
        moduleKey="hr"
      />
      <Card><CardContent className="pt-4 text-sm">{data ? <pre className="text-xs overflow-auto">{JSON.stringify(data, null, 2)}</pre> : "No report data."}</CardContent></Card>
    </div>
  );
}
