"use client";

import { useEffect, useMemo, useState } from "react";
import Link from "next/link";
import type { ResponsiveLayouts } from "react-grid-layout";
import { Loader2, Users, Shield, Ticket, Building2 } from "lucide-react";
import { useI18n } from "@/context/i18n-context";
import { useAnimation } from "@/context/animation-context";
import { useModule, type SubscribedModule } from "@/context/module-context";
import { getDashboardStats } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { CountingNumber } from "@/components/animate-ui/primitives/texts/counting-number";
import { Tilt, TiltContent } from "@/components/animate-ui/primitives/effects/tilt";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_tenant";

type Stats = {
  users_count?: number;
  roles_count?: number;
  tickets_open?: number;
  tickets_closed?: number;
  brands_count?: number;
  branches_count?: number;
};

const STAT_CARD_KEYS = ["users", "roles", "open_tickets", "brands"] as const;

function buildDefaultLayouts(statKeys: string[], moduleKeys: string[]): ResponsiveLayouts {
  const allKeys = [...statKeys, ...moduleKeys];
  const lg = allKeys.map((key, i) => {
    const isModule = moduleKeys.includes(key);
    const col = i % 4;
    const row = isModule ? Math.floor((i - statKeys.length) / 4) + 2 : 0;
    return { i: key, x: col * 3, y: row * 3, w: 3, h: isModule ? 3 : 2, minH: 2, minW: 2 };
  });
  const md = allKeys.map((key, i) => {
    const isModule = moduleKeys.includes(key);
    const col = i % 4;
    const row = isModule ? Math.floor((i - statKeys.length) / 4) + 2 : 0;
    return { i: key, x: col * 3, y: row * 3, w: 3, h: isModule ? 3 : 2, minH: 2, minW: 2 };
  });
  const sm = allKeys.map((key, i) => ({
    i: key, x: (i % 2) * 3, y: Math.floor(i / 2) * 3, w: 3, h: 3, minH: 2, minW: 2,
  }));
  const xs = allKeys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

export default function DashboardPage() {
  const { t } = useI18n();
  const { enabled: animationsEnabled } = useAnimation();
  const { subscribedModules, modulesLoading } = useModule();
  const [stats, setStats] = useState<Stats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getDashboardStats()
      .then((d) => setStats(d as Stats))
      .catch(() => setStats(null))
      .finally(() => setLoading(false));
  }, []);

  const cardDefs = useMemo(() => [
    { key: "users", label: t("dashboard.stats.users", "Users"), value: stats?.users_count ?? 0, icon: Users },
    { key: "roles", label: t("dashboard.stats.roles", "Roles"), value: stats?.roles_count ?? 0, icon: Shield },
    { key: "open_tickets", label: t("dashboard.stats.open_tickets", "Open Tickets"), value: stats?.tickets_open ?? 0, icon: Ticket },
    { key: "brands", label: t("dashboard.stats.brands", "Brands"), value: stats?.brands_count ?? 0, icon: Building2 },
  ], [stats, t]);

  const groupedBrands = useMemo(() => {
    return Object.values(
      subscribedModules.reduce((acc, mod) => {
        if (!mod.brand_id) return acc;
        if (!acc[mod.brand_id]) {
          acc[mod.brand_id] = {
            id: mod.brand_id,
            name: mod.brand_name,
            slug: mod.brand_slug,
            modules: [],
          };
        }
        if (!acc[mod.brand_id].modules.find((m: { module_key: string }) => m.module_key === mod.module_key)) {
          acc[mod.brand_id].modules.push({
            module_key: mod.module_key,
            name: mod.name,
            route: mod.route,
          });
        }
        return acc;
      }, {} as Record<number, { id: number; name: string | null; slug: string | null; modules: { module_key: string; name: string; route: string | null }[] }>)
    );
  }, [subscribedModules]);

  const brandKeys = useMemo(() => groupedBrands.map((b) => `brand:${b.id}`), [groupedBrands]);

  const defaultLayouts = useMemo<ResponsiveLayouts>(
    () => buildDefaultLayouts([...STAT_CARD_KEYS], brandKeys),
    [brandKeys]
  );

  if (loading) {
    return (
      <div className="flex min-h-[300px] items-center justify-center">
        <Loader2 className="size-8 animate-spin text-muted-foreground" />
      </div>
    );
  }

  const statCardElements = cardDefs.map((c) => {
    const cardContent = animationsEnabled ? (
      <Tilt key={c.key} className="rounded-xl h-full">
        <TiltContent className="h-full">
          <Card className="h-full">
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle className="text-sm font-medium">{c.label}</CardTitle>
              <c.icon className="size-4 text-muted-foreground" />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">
                <CountingNumber number={c.value} inView inViewOnce />
              </div>
            </CardContent>
          </Card>
        </TiltContent>
      </Tilt>
    ) : (
      <Card key={c.key} className="h-full">
        <CardHeader className="flex flex-row items-center justify-between pb-2">
          <CardTitle className="text-sm font-medium">{c.label}</CardTitle>
          <c.icon className="size-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div className="text-2xl font-bold">{c.value}</div>
        </CardContent>
      </Card>
    );
    return <div key={c.key} className="h-full">{cardContent}</div>;
  });

  const brandCardElements = modulesLoading
    ? []
    : groupedBrands.map((brand) => (
        <div
          key={`brand:${brand.id}`}
          className="h-full rounded-xl border bg-background p-4 transition-colors hover:bg-muted/50"
        >
          <Link
            href={brand.slug ? `/dashboard/brands/${brand.slug}` : `/dashboard/brands`}
            className="text-sm font-medium hover:underline"
          >
            {brand.name}
          </Link>
          <div className="mt-2 flex flex-wrap gap-1 items-center">
            {brand.modules.length > 0 && (
              <span className="text-xs text-muted-foreground mr-1">
                {t("dashboard.nav.modules", "Modules")}:
              </span>
            )}
            {brand.modules.map((mod) => (
              <Link
                key={`${brand.id}-${mod.module_key}`}
                href={mod.route ? `${mod.route}/${brand.slug}` : `/dashboard/modules/${mod.module_key}/${brand.slug}`}
                className="inline-flex items-center rounded-md bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary hover:bg-primary/20 transition-colors"
              >
                {mod.name}
              </Link>
            ))}
          </div>
        </div>
      ));

  const allItems = animationsEnabled
    ? (
        <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
          {[...statCardElements, ...brandCardElements]}
        </DraggableDashboardGrid>
      )
    : (
        <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
          {[...statCardElements, ...brandCardElements]}
        </DraggableDashboardGrid>
      );

  return (
    <div className="space-y-4">
      {modulesLoading && (
        <div className="flex min-h-[96px] items-center justify-center rounded-xl border bg-background">
          <Loader2 className="size-5 animate-spin text-muted-foreground" />
        </div>
      )}
      {!modulesLoading && allItems}
      {!modulesLoading && groupedBrands.length === 0 && statCardElements.length > 0 && (
        <p className="text-sm text-muted-foreground">
          {t("dashboard.brands.empty", "No brands with subscribed modules were found.")}
        </p>
      )}
    </div>
  );
}
