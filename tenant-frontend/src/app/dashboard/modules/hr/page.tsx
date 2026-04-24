"use client";

import { useEffect, useState } from "react";
import { Loader2, Briefcase } from "lucide-react";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getHrData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";

type HrData = { employees_count?: number; departments_count?: number; leave_requests_count?: number };

export default function HrPage() {
  const { t } = useI18n();
  const [data, setData] = useState<HrData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getHrData().then((d) => setData(d as HrData)).catch(() => toast.error("Failed to load HR data")).finally(() => setLoading(false));
  }, []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { label: t("dashboard.hr.employees", "Employees"), value: data?.employees_count ?? 0 },
    { label: t("dashboard.hr.departments", "Departments"), value: data?.departments_count ?? 0 },
    { label: t("dashboard.hr.leave_requests", "Leave Requests"), value: data?.leave_requests_count ?? 0 },
  ];

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <div className="flex items-center gap-2">
          <Briefcase className="size-5 text-muted-foreground" />
          <h1 className="text-xl font-semibold">{t("dashboard.hr.title", "HR Module")}</h1>
        </div>
        <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.hr.subtitle", "Human resources overview")}</p>
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
