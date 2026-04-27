"use client";

import { useEffect, useMemo, useState } from "react";
import { Loader2, Briefcase } from "lucide-react";
import type { ResponsiveLayouts } from "react-grid-layout";
import { ModulePageHeader } from "@/components/module-page-header";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { getHrData } from "@/lib/tenant-resources";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { ApexChart } from "@/components/ui/apex-chart";
import DraggableDashboardGrid from "@/components/dashboard/DraggableDashboardGrid";

const STORAGE_KEY = "dashboard_layout_hr";

function buildDefaultLayouts(): ResponsiveLayouts {
  const keys = ["employees", "departments", "leave_requests", "dept_dist", "leave_types", "attendance"];
  const lg = keys.map((key, i) => {
    const isChart = ["dept_dist", "leave_types", "attendance"].includes(key);
    const col = i % 3;
    const row = i < 3 ? 0 : 3;
    return { i: key, x: col * 4, y: row, w: 4, h: isChart ? 4 : 2, minH: 2, minW: 2 };
  });
  const md = keys.map((key, i) => {
    const isChart = ["dept_dist", "leave_types", "attendance"].includes(key);
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

type DeptDist = { department: string; count: number };
type AttendanceTrend = { date: string; present: number; absent: number; late: number };
type LeaveType = { leave_type: string; count: number };

type HrData = {
  employees_count?: number;
  departments_count?: number;
  leave_requests_count?: number;
  department_distribution?: DeptDist[];
  attendance_trends?: AttendanceTrend[];
  leave_types?: LeaveType[];
};

export default function HrPage() {
  const { t } = useI18n();
  const [data, setData] = useState<HrData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getHrData().then((d) => setData(d as HrData)).catch(() => toast.error("Failed to load HR data")).finally(() => setLoading(false));
  }, []);

  const defaultLayouts = useMemo(() => buildDefaultLayouts(), []);

  if (loading) return <div className="flex min-h-[200px] items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>;

  const cards = [
    { key: "employees", label: t("dashboard.hr.employees", "Employees"), value: data?.employees_count ?? 0 },
    { key: "departments", label: t("dashboard.hr.departments", "Departments"), value: data?.departments_count ?? 0 },
    { key: "leave_requests", label: t("dashboard.hr.leave_requests", "Leave Requests"), value: data?.leave_requests_count ?? 0 },
  ];

  const deptSeries = (data?.department_distribution ?? []).map((d) => ({ x: d.department, y: d.count }));
  const deptOptions = {
    chart: { id: "hr-departments" },
    labels: (data?.department_distribution ?? []).map((d) => d.department),
    title: { text: t("dashboard.hr.dept_title", "Employees by Department"), align: "left" as const },
    legend: { position: "bottom" as const },
  };

  const attendanceSeries = [
    { name: t("dashboard.hr.present", "Present"), data: (data?.attendance_trends ?? []).map((a) => a.present) },
    { name: t("dashboard.hr.absent", "Absent"), data: (data?.attendance_trends ?? []).map((a) => a.absent) },
    { name: t("dashboard.hr.late", "Late"), data: (data?.attendance_trends ?? []).map((a) => a.late) },
  ];
  const attendanceOptions = {
    chart: { id: "hr-attendance" },
    xaxis: { categories: (data?.attendance_trends ?? []).map((a) => a.date) },
    title: { text: t("dashboard.hr.attendance_title", "Attendance Trends (Last 14 Days)"), align: "left" as const },
  };

  const leaveSeries = [{
    name: t("dashboard.hr.requests", "Requests"),
    data: (data?.leave_types ?? []).map((l) => l.count),
  }];
  const leaveOptions = {
    chart: { id: "hr-leave-types" },
    xaxis: { categories: (data?.leave_types ?? []).map((l) => l.leave_type) },
    plotOptions: { bar: { borderRadius: 4 } },
    title: { text: t("dashboard.hr.leave_title", "Leave Requests by Type"), align: "left" as const },
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
    <div key="dept_dist" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="donut" series={deptSeries} options={deptOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
    <div key="leave_types" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="bar" series={leaveSeries} options={leaveOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
    <div key="attendance" className="h-full">
      <Card className="h-full">
        <CardContent className="pt-4">
          <ApexChart type="area" series={attendanceSeries} options={attendanceOptions} height={300} />
        </CardContent>
      </Card>
    </div>,
  ];

  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Briefcase}
        titleKey="dashboard.hr.title"
        titleFallback="HR Module"
        subtitleKey="dashboard.hr.subtitle"
        subtitleFallback="Human resources overview"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <DraggableDashboardGrid storageKey={STORAGE_KEY} defaultLayouts={defaultLayouts}>
        {[...statCards, ...chartCards]}
      </DraggableDashboardGrid>
    </div>
  );
}
