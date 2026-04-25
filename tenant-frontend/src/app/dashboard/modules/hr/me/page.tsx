"use client";

import { useEffect, useState } from "react";
import { UserCircle } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { getMyHrProfile } from "@/lib/api-hr";

export default function HrMePage() {
  const [profile, setProfile] = useState<Record<string, unknown> | null>(null);

  useEffect(() => {
    getMyHrProfile().then((res) => setProfile((res?.data ?? res) as Record<string, unknown>)).catch(() => setProfile(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={UserCircle}
        titleKey="dashboard.hr.me"
        titleFallback="My HR Profile"
        subtitleKey="dashboard.hr.me_subtitle"
        subtitleFallback="Personal employee profile and self-service"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm">
          {profile ? <pre className="overflow-auto text-xs">{JSON.stringify(profile, null, 2)}</pre> : "Unable to load profile."}
        </CardContent>
      </Card>
    </div>
  );
}
