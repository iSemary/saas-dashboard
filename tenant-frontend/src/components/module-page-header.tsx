"use client";

import type { LucideIcon } from "lucide-react";
import { LayoutDashboard } from "lucide-react";
import { useRouter } from "next/navigation";
import { useI18n } from "@/context/i18n-context";
import { useModule } from "@/context/module-context";
import { resolveIcon } from "@/lib/lucide-icon-map";
import { Button } from "@/components/ui/button";

interface ModulePageHeaderProps {
  icon?: LucideIcon;
  titleKey: string;
  titleFallback: string;
  subtitleKey: string;
  subtitleFallback: string;
  dashboardHref: string;
  moduleKey?: string;
}

export function ModulePageHeader({
  icon: IconProp,
  titleKey,
  titleFallback,
  subtitleKey,
  subtitleFallback,
  dashboardHref,
  moduleKey,
}: ModulePageHeaderProps) {
  const { t } = useI18n();
  const router = useRouter();
  const { getModuleByKey } = useModule();

  const moduleData = moduleKey ? getModuleByKey(moduleKey) : undefined;
  const Icon = IconProp ?? (moduleKey ? resolveIcon(moduleKey === "crm" ? "UsersRound" : moduleKey === "hr" ? "Briefcase" : moduleKey === "pos" ? "ShoppingCart" : null) : LayoutDashboard);
  const title = moduleData ? moduleData.name : t(titleKey, titleFallback);
  const subtitle = moduleData?.slogan ?? moduleData?.description ?? t(subtitleKey, subtitleFallback);

  return (
    <div className="rounded-xl border bg-muted/40 p-4">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-2">
          <Icon className="size-5 text-muted-foreground" />
          <h1 className="text-xl font-semibold">{title}</h1>
        </div>
        <Button variant="outline" size="sm" onClick={() => router.push(dashboardHref)}>
          <LayoutDashboard className="size-4" />
          {t("dashboard.modules.open_dashboard", "Open Dashboard")}
        </Button>
      </div>
      <p className="mt-1 text-sm text-muted-foreground">{subtitle}</p>
    </div>
  );
}
