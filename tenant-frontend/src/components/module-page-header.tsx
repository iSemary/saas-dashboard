"use client";

import type { LucideIcon } from "lucide-react";

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
  return null;
}
