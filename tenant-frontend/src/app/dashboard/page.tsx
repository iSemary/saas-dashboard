"use client";

import { useEffect, useState } from "react";
import { Loader2, Users, Shield, Ticket, Building2 } from "lucide-react";
import { useI18n } from "@/context/i18n-context";
import { getDashboardStats } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

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
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.nav.overview", "Dashboard")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.subtitle", "Overview of your tenant.")}</p>
      </div>
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {cards.map((c) => (
          <Card key={c.label}>
            <CardHeader className="flex flex-row items-center justify-between pb-2">
              <CardTitle className="text-sm font-medium">{c.label}</CardTitle>
              <c.icon className="size-4 text-muted-foreground" />
            </CardHeader>
            <CardContent><div className="text-2xl font-bold">{c.value}</div></CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
