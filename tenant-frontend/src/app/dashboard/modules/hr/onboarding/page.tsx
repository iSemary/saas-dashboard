"use client";

import { ClipboardCheck } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";

export default function HrOnboardingPage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={ClipboardCheck}
        titleKey="dashboard.hr.onboarding"
        titleFallback="Onboarding"
        subtitleKey="dashboard.hr.onboarding_subtitle"
        subtitleFallback="Manage onboarding and offboarding processes"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm text-muted-foreground">
          Onboarding templates and process tracking are now available via HR onboarding APIs.
        </CardContent>
      </Card>
    </div>
  );
}
