"use client";

import { FileText } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { useI18n } from "@/context/i18n-context";
import { Card, CardContent } from "@/components/ui/card";

export default function HrLeaveRequestsPage() {
  const { t } = useI18n();
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={FileText}
        titleKey="dashboard.hr.leave_requests"
        titleFallback="Leave Requests"
        subtitleKey="dashboard.hr.leave_requests_subtitle"
        subtitleFallback="Manage leave requests"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="flex min-h-[200px] items-center justify-center text-muted-foreground">
          {t("dashboard.coming_soon", "Coming soon — this page is under development.")}
        </CardContent>
      </Card>
    </div>
  );
}
