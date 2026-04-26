"use client";

import { useEffect, useState } from "react";
import { Loader2, FolderKanban } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getProjectManagementData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type PmData = {
  projects_count?: number;
  active_tasks_count?: number;
  overdue_tasks_count?: number;
  completed_milestones_count?: number;
};

export default function ProjectManagementPage() {
  const { t } = useI18n();
  const [data, setData] = useState<PmData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getProjectManagementData()
      .then((d) => setData(d as PmData))
      .catch(() => toast.error("Failed to load Project Management data"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  const cards = [
    { label: t("dashboard.pm.projects", "Projects"), value: data?.projects_count ?? 0 },
    { label: t("dashboard.pm.active_tasks", "Active Tasks"), value: data?.active_tasks_count ?? 0 },
    { label: t("dashboard.pm.overdue", "Overdue"), value: data?.overdue_tasks_count ?? 0 },
    { label: t("dashboard.pm.milestones", "Completed Milestones"), value: data?.completed_milestones_count ?? 0 },
  ];

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={FolderKanban}
        titleKey="dashboard.pm.title"
        titleFallback="Project Management"
        subtitleKey="dashboard.pm.subtitle"
        subtitleFallback="Track projects, tasks, and delivery progress"
        dashboardHref="/dashboard/modules/project-management"
        moduleKey="project_management"
      />
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {cards.map((c) => (
          <Card key={c.label}>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm font-medium">{c.label}</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{c.value}</div>
            </CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
