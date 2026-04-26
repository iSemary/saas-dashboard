"use client";

import type { LucideIcon } from "lucide-react";
import { X, Building2 } from "lucide-react";
import Link from "next/link";
import { useBrandFilter } from "@/context/brand-filter-context";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

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
  titleFallback,
  subtitleFallback,
  dashboardHref,
}: ModulePageHeaderProps) {
  const { t } = useI18n();
  const { brandFilter, isBrandFiltered, clearBrandFilter } = useBrandFilter();

  return (
    <div className="space-y-2">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          {IconProp && <IconProp className="size-6 text-primary" />}
          <div>
            <h1 className="text-xl font-semibold">{titleFallback}</h1>
            <p className="text-sm text-muted-foreground">{subtitleFallback}</p>
          </div>
        </div>
        <Link href={dashboardHref}>
          <Button variant="outline" size="sm">
            {t("dashboard.actions.back_to_dashboard", "Back to Dashboard")}
          </Button>
        </Link>
      </div>

      {isBrandFiltered && brandFilter && (
        <div className="flex items-center gap-2">
          <Badge variant="secondary" className="gap-1.5 px-2.5 py-1">
            <Building2 className="size-3" />
            <span>
              {t("dashboard.viewing_brand", "Viewing")}: {brandFilter.name}
            </span>
            <Button
              variant="ghost"
              size="icon"
              className="-mr-1 h-4 w-4 p-0 hover:bg-transparent"
              onClick={clearBrandFilter}
              title={t("dashboard.clear_filter", "Clear filter")}
            >
              <X className="size-3" />
            </Button>
          </Badge>
        </div>
      )}
    </div>
  );
}
