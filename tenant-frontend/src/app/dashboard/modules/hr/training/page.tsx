"use client";

import { GraduationCap } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";

export default function HrTrainingPage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={GraduationCap}
        titleKey="dashboard.hr.training"
        titleFallback="Training"
        subtitleKey="dashboard.hr.training_subtitle"
        subtitleFallback="Manage courses, enrollments, and certifications"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm text-muted-foreground">
          Training APIs are wired for courses, enrollments, and certifications.
        </CardContent>
      </Card>
    </div>
  );
}
