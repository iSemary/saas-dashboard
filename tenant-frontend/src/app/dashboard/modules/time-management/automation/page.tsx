"use client";

import { Zap } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";

export default function AutomationPage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Zap}
        titleKey="tm.automation"
        titleFallback="Automation"
        subtitleKey="tm.automation_subtitle"
        subtitleFallback="Automate time management workflows"
        dashboardHref="/dashboard/modules/time-management"
        moduleKey="time_management"
      />
      <div className="flex min-h-[300px] items-center justify-center text-muted-foreground">
        <div className="text-center space-y-2">
          <Zap className="size-8 mx-auto opacity-30" />
          <p>Automation rules coming soon</p>
          <p className="text-sm">Define triggers and actions to automate time tracking workflows</p>
        </div>
      </div>
    </div>
  );
}
