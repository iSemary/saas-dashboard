"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { Loader2, Users, Shield, Ticket, Building2 } from "lucide-react";
import { useI18n } from "@/context/i18n-context";
import { useAnimation } from "@/context/animation-context";
import { useModule, type SubscribedModule } from "@/context/module-context";
import { getDashboardStats } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { CountingNumber } from "@/components/animate-ui/primitives/texts/counting-number";
import { Fades } from "@/components/animate-ui/primitives/effects/fade";
import { Tilt, TiltContent } from "@/components/animate-ui/primitives/effects/tilt";

type Stats = {
  users_count?: number;
  roles_count?: number;
  tickets_open?: number;
  tickets_closed?: number;
  brands_count?: number;
  branches_count?: number;
};

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

  if (loading) {
    return <div className="flex min-h-[300px] items-center justify-center"><Loader2 className="size-8 animate-spin text-muted-foreground" /></div>;
  }

  const cards = [
    { label: t("dashboard.stats.users", "Users"), value: stats?.users_count ?? 0, icon: Users },
    { label: t("dashboard.stats.roles", "Roles"), value: stats?.roles_count ?? 0, icon: Shield },
    { label: t("dashboard.stats.open_tickets", "Open Tickets"), value: stats?.tickets_open ?? 0, icon: Ticket },
    { label: t("dashboard.stats.brands", "Brands"), value: stats?.brands_count ?? 0, icon: Building2 },
  ];

  return (
    <div className="space-y-6">
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {animationsEnabled ? (
          <Fades holdDelay={100}>
            {cards.map((c) => (
              <Tilt key={c.label} className="rounded-xl">
                <TiltContent>
                  <Card>
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
            ))}
          </Fades>
        ) : (
          cards.map((c) => (
            <Card key={c.label}>
              <CardHeader className="flex flex-row items-center justify-between pb-2">
                <CardTitle className="text-sm font-medium">{c.label}</CardTitle>
                <c.icon className="size-4 text-muted-foreground" />
              </CardHeader>
              <CardContent>
                <div className="text-2xl font-bold">{c.value}</div>
              </CardContent>
            </Card>
          ))
        )}
      </div>
      <div className="space-y-3">
        <h2 className="text-lg font-semibold">{t("dashboard.nav.section_modules", "Modules")}</h2>
        {modulesLoading ? (
          <div className="flex min-h-[96px] items-center justify-center rounded-xl border bg-background">
            <Loader2 className="size-5 animate-spin text-muted-foreground" />
          </div>
        ) : subscribedModules.length > 0 ? (
          <div className="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            {Object.values(
              subscribedModules.reduce((acc, mod) => {
                if (!acc[mod.module_key]) {
                  acc[mod.module_key] = {
                    ...mod,
                    brand_names: [],
                  };
                }
                if (mod.brand_name && !acc[mod.module_key].brand_names.includes(mod.brand_name)) {
                  acc[mod.module_key].brand_names.push(mod.brand_name);
                }
                return acc;
              }, {} as Record<string, SubscribedModule & { brand_names: string[]}>)
            ).map((mod) => (
              <Link
                key={mod.module_key}
                href={mod.route ?? `/dashboard/modules/${mod.module_key}`}
                className="rounded-xl border bg-background p-4 transition-colors hover:bg-muted/50"
              >
                <div className="text-sm font-medium">{mod.name}</div>
                <div className="mt-2 text-xs text-muted-foreground">
                  {mod.brand_names.length > 0
                    ? `${t("dashboard.nav.brands", "Brands")}: ${mod.brand_names.join(", ")}`
                    : ""}
                </div>
              </Link>
            ))}
          </div>
        ) : (
          <div className="rounded-xl border bg-muted/30 p-4 text-sm text-muted-foreground">
            {t("dashboard.modules.empty", "No subscribed modules were found for your tenant brands.")}
          </div>
        )}
      </div>
    </div>
  );
}
