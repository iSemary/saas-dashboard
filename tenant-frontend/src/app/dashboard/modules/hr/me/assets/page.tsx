"use client";

import { useEffect, useState } from "react";
import { Package } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";
import { getMyHrAssets } from "@/lib/api-hr";

export default function HrMyAssetsPage() {
  const [data, setData] = useState<unknown>(null);

  useEffect(() => {
    getMyHrAssets().then((res) => setData(res)).catch(() => setData(null));
  }, []);

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Package}
        titleKey="dashboard.hr.me_assets"
        titleFallback="My Assets"
        subtitleKey="dashboard.hr.me_assets_subtitle"
        subtitleFallback="View currently assigned company assets"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm">
          {data ? <pre className="overflow-auto text-xs">{JSON.stringify(data, null, 2)}</pre> : "Unable to load assets data."}
        </CardContent>
      </Card>
    </div>
  );
}
