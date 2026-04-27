"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, FolderKanban } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getProjectManagementData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_project_management";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["projects", "active_tasks", "overdue", "milestones"];
  const lg = keys.map((key, i) => ({
    i: key, x: i * 3, y: 0, w: 3, h: 2, minH: 2, minW: 2,
  }));
  const md = keys.map((key, i) => ({
    i: key, x: i * 3, y: 0, w: 3, h: 2, minH: 2, minW: 2,
  }));
  const sm = keys.map((key, i) => ({
    i: key, x: (i % 2) * 6, y: Math.floor(i / 2) * 3, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const xs = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

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

  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  const cards = [
    { key: "projects", label: t("dashboard.pm.projects", "Projects"), value: data?.projects_count ?? 0 },
    { key: "active_tasks", label: t("dashboard.pm.active_tasks", "Active Tasks"), value: data?.active_tasks_count ?? 0 },
    { key: "overdue", label: t("dashboard.pm.overdue", "Overdue"), value: data?.overdue_tasks_count ?? 0 },
    { key: "milestones", label: t("dashboard.pm.milestones", "Completed Milestones"), value: data?.completed_milestones_count ?? 0 },
  ];

  const statCards = cards.map((c) => (
    <div key={c.key} className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2">
          <CardTitle className="text-sm font-medium">{c.label}</CardTitle>
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{c.value}</div>
        </CardContent>
      </Card>
    </div>
  ));

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
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {statCards}
      </DraggableDashboardGrid>
    </div>
  );
}
