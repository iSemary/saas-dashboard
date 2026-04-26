"use client";

import { Loader2, GanttChart } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";

export default function TimelinePage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={GanttChart}
        titleKey="pm.timeline"
        titleFallback="Timeline"
        subtitleKey="pm.timeline_subtitle"
        subtitleFallback="Gantt-style timeline view for project schedules"
        dashboardHref="/dashboard/modules/project-management"
        moduleKey="project_management"
      />
      <div className="flex min-h-[300px] items-center justify-center text-muted-foreground">
        <div className="text-center space-y-2">
          <Loader2 className="size-8 mx-auto opacity-30" />
          <p>Timeline view coming soon</p>
          <p className="text-sm">Select a project to view its Gantt chart timeline</p>
        </div>
      </div>
    </div>
  );
}
