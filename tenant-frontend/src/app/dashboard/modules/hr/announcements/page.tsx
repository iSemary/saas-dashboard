"use client";

import { Megaphone } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";

export default function HrAnnouncementsPage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Megaphone}
        titleKey="dashboard.hr.announcements"
        titleFallback="Announcements & Policies"
        subtitleKey="dashboard.hr.announcements_subtitle"
        subtitleFallback="Publish HR announcements and policies"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm text-muted-foreground">
          Announcement and policy endpoints are available via HR communication APIs.
        </CardContent>
      </Card>
    </div>
  );
}
