"use client";

import { Package } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";

export default function HrAssetsPage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Package}
        titleKey="dashboard.hr.assets"
        titleFallback="Assets"
        subtitleKey="dashboard.hr.assets_subtitle"
        subtitleFallback="Track asset inventory and employee assignments"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm text-muted-foreground">
          Asset categories, assets, and assignments are exposed through HR assets endpoints.
        </CardContent>
      </Card>
    </div>
  );
}
