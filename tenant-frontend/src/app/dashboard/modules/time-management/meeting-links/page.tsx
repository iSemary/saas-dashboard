"use client";

import { useEffect, useState } from "react";
import { Video, RefreshCw } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { listTmMeetingLinks, regenerateTmMeetingLink } from "@/lib/tm-resources";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type MeetingLink = { id: number; url: string; provider: string; created_at: string };

export default function MeetingLinksPage() {
  const [links, setLinks] = useState<MeetingLink[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    listTmMeetingLinks<MeetingLink>()
      .then((r) => setLinks(r.data ?? []))
      .catch(() => toast.error("Failed to load meeting links"))
      .finally(() => setLoading(false));
  }, []);

  const handleRegenerate = async (id: number) => {
    try {
      await regenerateTmMeetingLink(id);
      toast.success("Meeting link regenerated");
    } catch {
      toast.error("Failed to regenerate link");
    }
  };

  if (loading) {
    return <div className="flex min-h-[200px] items-center justify-center"><div className="size-6 animate-spin rounded-full border-2 border-primary border-t-transparent" /></div>;
  }

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Video}
        titleKey="tm.meeting_links"
        titleFallback="Meeting Links"
        subtitleKey="tm.meeting_links_subtitle"
        subtitleFallback="Manage your meeting links"
        dashboardHref="/dashboard/modules/time-management"
        moduleKey="time_management"
      />
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {links.map((l) => (
          <Card key={l.id}>
            <CardHeader className="pb-2">
              <CardTitle className="text-sm font-medium">{l.provider}</CardTitle>
            </CardHeader>
            <CardContent>
              <a href={l.url} target="_blank" rel="noopener noreferrer" className="text-xs text-blue-600 underline break-all">{l.url}</a>
              <div className="mt-2">
                <Button variant="outline" size="sm" onClick={() => handleRegenerate(l.id)}>
                  <RefreshCw className="mr-1 size-3" /> Regenerate
                </Button>
              </div>
            </CardContent>
          </Card>
        ))}
        {links.length === 0 && (
          <div className="col-span-full text-center text-muted-foreground py-8">No meeting links found.</div>
        )}
      </div>
    </div>
  );
}
