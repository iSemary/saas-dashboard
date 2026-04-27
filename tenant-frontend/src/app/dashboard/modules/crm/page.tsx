"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, UsersRound } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getCrmData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ApexChart } from "@/components/ui/apex-chart";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_crm";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["contacts", "companies", "deals", "pipeline", "sources", "monthly"];
  const lg = keys.map((key, i) => {
    const isChart = ["pipeline", "sources", "monthly"].includes(key);
    const col = i % 3;
    const row = i < 3 ? 0 : 3;
    return { i: key, x: col * 4, y: row, w: 4, h: isChart ? 4 : 2, minH: 2, minW: 2 };
  });
  const md = keys.map((key, i) => {
    const isChart = ["pipeline", "sources", "monthly"].includes(key);
    const col = i % 3;
    const row = i < 3 ? 0 : 3;
    return { i: key, x: col * 4, y: row, w: 4, h: isChart ? 4 : 2, minH: 2, minW: 2 };
  });
  const sm = keys.map((key, i) => ({
    i: key, x: (i % 2) * 6, y: Math.floor(i / 2) * 3, w: 6, h: 3, minH: 2, minW: 2,
  }));
  const xs = keys.map((key, i) => ({
    i: key, x: 0, y: i * 3, w: 4, h: 3, minH: 2, minW: 2,
  }));
  return { lg, md, sm, xs };
}

type PipelineStage = { stage: string; count: number; total_value: number };
type LeadSource = { source: string; count: number };
type MonthlyLead = { month: string; count: number };

type CrmData = {
  contacts_count?: number;
  companies_count?: number;
  deals_count?: number;
  pipeline_stages?: PipelineStage[];
  leads_by_source?: LeadSource[];
  monthly_leads?: MonthlyLead[];
};

const STAGE_LABELS: Record<string, string> = {
  prospecting: "Prospecting",
  qualification: "Qualification",
  proposal: "Proposal",
  negotiation: "Negotiation",
  closed_won: "Closed Won",
  closed_lost: "Closed Lost",
};

export default function CrmPage() {
  const { t } = useI18n();
  const [data, setData] = useState<CrmData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getCrmData().then((d) => setData(d as CrmData)).catch(() => toast.error("Failed to load CRM data")).finally(() => setLoading(false));
  }, []);

  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { key: "contacts", label: t("dashboard.crm.contacts", "Contacts"), value: data?.contacts_count ?? 0 },
    { key: "companies", label: t("dashboard.crm.companies", "Companies"), value: data?.companies_count ?? 0 },
    { key: "deals", label: t("dashboard.crm.deals", "Deals"), value: data?.deals_count ?? 0 },
  ];

  const pipelineSeries = [{
    name: t("dashboard.crm.pipeline_count", "Opportunities"),
    data: (data?.pipeline_stages ?? []).map((s) => s.count),
  }];

  const pipelineOptions = {
    chart: { id: "crm-pipeline" },
    xaxis: {
      categories: (data?.pipeline_stages ?? []).map((s) => STAGE_LABELS[s.stage] ?? s.stage),
    },
    plotOptions: { bar: { borderRadius: 4 } },
    title: { text: t("dashboard.crm.pipeline_title", "Sales Pipeline"), align: "left" as const },
  };

  const sourceSeries = (data?.leads_by_source ?? []).map((s) => ({ x: s.source, y: s.count }));
  const sourceOptions = {
    chart: { id: "crm-sources" },
    labels: (data?.leads_by_source ?? []).map((s) => s.source),
    title: { text: t("dashboard.crm.leads_source_title", "Leads by Source"), align: "left" as const },
    legend: { position: "bottom" as const },
  };

  const monthlySeries = [{
    name: t("dashboard.crm.leads", "Leads"),
    data: (data?.monthly_leads ?? []).map((m) => m.count),
  }];

  const monthlyOptions = {
    chart: { id: "crm-monthly-leads" },
    xaxis: {
      categories: (data?.monthly_leads ?? []).map((m) => m.month),
    },
    title: { text: t("dashboard.crm.monthly_leads_title", "Monthly Leads Trend"), align: "left" as const },
  };

  const statCards = cards.map((c) => (
    <div key={c.key} className="h-full">
      <Card className="h-full">
        <CardHeader className="pb-2"><CardTitle className="text-sm font-medium">{c.label}</CardTitle></CardHeader>
        <CardContent><div className="text-2xl font-bold">{c.value}</div></CardContent>
      </Card>
    </div>
  ));

  const chartCards = [
    <div key="pipeline" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="bar" series={pipelineSeries} options={pipelineOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
    <div key="sources" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="donut" series={sourceSeries} options={sourceOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
    <div key="monthly" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="area" series={monthlySeries} options={monthlyOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
  ];

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={UsersRound}
        titleKey="dashboard.crm.title"
        titleFallback="CRM Module"
        subtitleKey="dashboard.crm.subtitle"
        subtitleFallback="Customer relationship management overview"
        dashboardHref="/dashboard/modules/crm"
        moduleKey="crm"
      />
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {[...statCards, ...chartCards]}
      </DraggableDashboardGrid>
    </div>
  );
}
