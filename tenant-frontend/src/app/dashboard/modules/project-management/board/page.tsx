"use client";

import { useEffect, useState } from "react";
import { Loader2, KanbanSquare } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { listPmProjects } from "@/lib/pm-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type Project = { id: number; name: string; key: string; status: string };

export default function BoardPage() {
  const [projects, setProjects] = useState<Project[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    listPmProjects<Project>()
      .then((r) => setProjects(r.data ?? []))
      .catch(() => toast.error("Failed to load projects"))
      .finally(() => setLoading(false));
  }, []);

  if (loading) {
    return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;
  }

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={KanbanSquare}
        titleKey="pm.board"
        titleFallback="Board"
        subtitleKey="pm.board_subtitle"
        subtitleFallback="Kanban board view for your projects"
        dashboardHref="/dashboard/modules/project-management"
        moduleKey="project_management"
      />
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {projects.map((p) => (
          <Card key={p.id} className="cursor-pointer hover:shadow-md transition-shadow">
            <CardHeader className="pb-2">
              <CardTitle className="text-sm font-medium">{p.name}</CardTitle>
            </CardHeader>
            <CardContent>
              <div className="text-xs text-muted-foreground">{p.key} · {p.status}</div>
            </CardContent>
          </Card>
        ))}
        {projects.length === 0 && (
          <div className="col-span-full text-center text-muted-foreground py-8">
            No projects found. Create a project first to use the board view.
          </div>
        )}
      </div>
    </div>
  );
}
