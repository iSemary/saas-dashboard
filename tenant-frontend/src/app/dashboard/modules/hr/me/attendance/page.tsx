"use client";

import { useEffect, useState } from "react";
import { Clock } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { getMyHrAttendance } from "@/lib/api-hr";

export default function HrMyAttendancePage() {
  const [data, setData] = useState<unknown>(null);

  useEffect(() => {
    getMyHrAttendance().then((res) => setData(res)).catch(() => setData(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Clock}
        titleKey="dashboard.hr.me_attendance"
        titleFallback="My Attendance"
        subtitleKey="dashboard.hr.me_attendance_subtitle"
        subtitleFallback="Review your attendance records"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm">
          {data ? <pre className="overflow-auto text-xs">{JSON.stringify(data, null, 2)}</pre> : "Unable to load attendance data."}
        </CardContent>
      </Card>
    </div>
  );
}
