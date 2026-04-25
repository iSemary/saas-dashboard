"use client";

import { useEffect, useState } from "react";
import { Wallet } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { getMyHrPayroll } from "@/lib/api-hr";

export default function HrMyPayrollPage() {
  const [data, setData] = useState<unknown>(null);

  useEffect(() => {
    getMyHrPayroll().then((res) => setData(res)).catch(() => setData(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Wallet}
        titleKey="dashboard.hr.me_payroll"
        titleFallback="My Payroll"
        subtitleKey="dashboard.hr.me_payroll_subtitle"
        subtitleFallback="Track your payroll history"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm">
          {data ? <pre className="overflow-auto text-xs">{JSON.stringify(data, null, 2)}</pre> : "Unable to load payroll data."}
        </CardContent>
      </Card>
    </div>
  );
}
