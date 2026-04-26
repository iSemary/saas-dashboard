"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { Loader2 } from "lucide-react";
import api from "@/lib/api";

type ReportData = Record<string, unknown>;

export default function AccountingReportsPage() {
  const [reportType, setReportType] = useState<string>("trial_balance");
  const [report, setReport] = useState<ReportData | null>(null);
  const [loading, setLoading] = useState(true);
  const abortRef = useRef<AbortController | null>(null);

  const fetchReport = useCallback(async (type: string) => {
    abortRef.current?.abort();
    const controller = new AbortController();
    abortRef.current = controller;
    try {
      const { data: resp } = await api.get(`/tenant/accounting/reports/${type}`, { signal: controller.signal });
      if (!controller.signal.aborted) {
        setReport(resp.data);
        setLoading(false);
      }
    } catch {
      if (!controller.signal.aborted) {
        setReport(null);
        setLoading(false);
      }
    }
  }, []);

  useEffect(() => {
    void fetchReport(reportType);
    return () => { abortRef.current?.abort(); };
  }, [reportType, fetchReport]);

  const reportTypes = [
    { value: "trial_balance", label: "Trial Balance" },
    { value: "profit_loss", label: "Profit & Loss" },
    { value: "balance_sheet", label: "Balance Sheet" },
    { value: "cash_flow", label: "Cash Flow" },
  ];

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">Financial Reports</h1>
        <p className="mt-1 text-sm text-muted-foreground">Generate and view financial reports</p>
      </div>

      <div className="flex gap-2">
        {reportTypes.map((rt) => (
          <button
            key={rt.value}
            onClick={() => setReportType(rt.value)}
            className={`rounded-lg px-4 py-2 text-sm font-medium transition-colors ${
              reportType === rt.value
                ? "bg-primary text-primary-foreground"
                : "border border-border bg-background hover:bg-muted"
            }`}
          >
            {rt.label}
          </button>
        ))}
      </div>

      {loading ? (
        <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
          <Loader2 className="size-6 animate-spin" />
        </div>
      ) : report ? (
        <div className="rounded-xl border bg-card p-6">
          <h2 className="mb-4 text-lg font-semibold capitalize">{reportType.replace("_", " ")}</h2>
          <pre className="overflow-auto rounded-lg bg-muted/50 p-4 text-sm">
            {JSON.stringify(report, null, 2)}
          </pre>
        </div>
      ) : (
        <div className="rounded-xl border bg-card p-6 text-center text-muted-foreground">
          No report data available. Make sure you have posted journal entries.
        </div>
      )}
    </div>
  );
}
