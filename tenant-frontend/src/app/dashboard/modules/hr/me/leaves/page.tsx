"use client";

import { useEffect, useState } from "react";
import { CalendarRange } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { getMyHrLeaves } from "@/lib/api-hr";

export default function HrMyLeavesPage() {
  const [data, setData] = useState<unknown>(null);

  useEffect(() => {
    getMyHrLeaves().then((res) => setData(res?.data ?? res)).catch(() => setData(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={CalendarRange}
        titleKey="dashboard.hr.me_leaves"
        titleFallback="My Leaves"
        subtitleKey="dashboard.hr.me_leaves_subtitle"
        subtitleFallback="Track your personal leave requests"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm">
          {data ? <pre className="overflow-auto text-xs">{JSON.stringify(data, null, 2)}</pre> : "Unable to load leave data."}
        </CardContent>
      </Card>
    </div>
  );
}
