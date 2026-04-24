"use client";

import { useEffect, useState } from "react";
import { Loader2, ShoppingCart } from "lucide-react";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getPosData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type PosData = { products_count?: number; orders_count?: number; revenue_today?: number };

export default function PosPage() {
  const { t } = useI18n();
  const [data, setData] = useState<PosData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getPosData().then((d) => setData(d as PosData)).catch(() => toast.error("Failed to load POS data")).finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { label: t("dashboard.pos.products", "Products"), value: data?.products_count ?? 0 },
    { label: t("dashboard.pos.orders", "Orders"), value: data?.orders_count ?? 0 },
    { label: t("dashboard.pos.revenue_today", "Revenue Today"), value: `$${(data?.revenue_today ?? 0).toLocaleString()}` },
  ];

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <div className="flex items-center gap-2">
          <ShoppingCart className="size-5 text-muted-foreground" />
          <h1 className="text-xl font-semibold">{t("dashboard.pos.title", "POS Module")}</h1>
        </div>
        <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.pos.subtitle", "Point of sale overview")}</p>
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
