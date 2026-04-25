"use client";

import { useEffect, useState } from "react";
import { Target } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { getMyHrGoals } from "@/lib/api-hr";

export default function HrMyGoalsPage() {
  const [data, setData] = useState<unknown>(null);

  useEffect(() => {
    getMyHrGoals().then((res) => setData(res)).catch(() => setData(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Target}
        titleKey="dashboard.hr.me_goals"
        titleFallback="My Goals"
        subtitleKey="dashboard.hr.me_goals_subtitle"
        subtitleFallback="Monitor your performance goals"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm">
          {data ? <pre className="overflow-auto text-xs">{JSON.stringify(data, null, 2)}</pre> : "Unable to load goals data."}
        </CardContent>
      </Card>
    </div>
  );
}
