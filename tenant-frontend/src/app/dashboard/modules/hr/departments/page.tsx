"use client";

import { Building2 } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { useI18n } from "@/context/i18n-context";
import { Card, CardContent } from "@/components/ui/card";

export default function HrDepartmentsPage() {
  const { t } = useI18n();
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Building2}
        titleKey="dashboard.hr.departments"
        titleFallback="Departments"
        subtitleKey="dashboard.hr.departments_subtitle"
        subtitleFallback="Manage your departments"
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
