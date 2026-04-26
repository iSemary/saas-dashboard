"use client";

import { Zap } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";

export default function AutomationPage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Zap}
        titleKey="pm.automation"
        titleFallback="Automation"
        subtitleKey="pm.automation_subtitle"
        subtitleFallback="Automate project workflows and triggers"
        dashboardHref="/dashboard/modules/project-management"
        moduleKey="project_management"
      />
      <div className="flex min-h-[300px] items-center justify-center text-muted-foreground">
        <div className="text-center space-y-2">
          <Zap className="size-8 mx-auto opacity-30" />
          <p>Automation rules coming soon</p>
          <p className="text-sm">Define triggers and actions to automate project workflows</p>
        </div>
      </div>
    </div>
  );
}
