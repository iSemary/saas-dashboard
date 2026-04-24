"use client";

import { useEffect, useState } from "react";
import { Loader2, UsersRound } from "lucide-react";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getCrmData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type CrmData = { contacts_count?: number; companies_count?: number; deals_count?: number };

export default function CrmPage() {
  const { t } = useI18n();
  const [data, setData] = useState<CrmData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getCrmData().then((d) => setData(d as CrmData)).catch(() => toast.error("Failed to load CRM data")).finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { label: t("dashboard.crm.contacts", "Contacts"), value: data?.contacts_count ?? 0 },
    { label: t("dashboard.crm.companies", "Companies"), value: data?.companies_count ?? 0 },
    { label: t("dashboard.crm.deals", "Deals"), value: data?.deals_count ?? 0 },
  ];

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <div className="flex items-center gap-2">
          <UsersRound className="size-5 text-muted-foreground" />
          <h1 className="text-xl font-semibold">{t("dashboard.crm.title", "CRM Module")}</h1>
        </div>
        <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.crm.subtitle", "Customer relationship management overview")}</p>
      </div>
      <div className="grid gap-4 sm:grid-cols-3">
        {cards.map((c) => (
          <Card key={c.label}>
            <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">{c.label}</CardTitle></CardHeader>
            <CardContent><div className="text-2xl font-bold">{c.value}</div></CardContent>
          </Card>
        ))}
      </div>
    </div>
  );
}
