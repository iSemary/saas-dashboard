"use client";

import { useCallback, useEffect, useRef, useState } from "react";
import { useSearchParams, useRouter } from "next/navigation";
import { Loader2, ArrowLeft, CheckCircle, XCircle, Send } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { Button } from "@/components/ui/button";

type Expense = { id: number; title: string; amount: number; currency: string; date: string; status: string; vendor: string; category_id: number };
type Report = {
  id: number;
  title: string;
  description: string;
  status: string;
  total_amount: number;
  created_at: string;
  expenses: Expense[];
};

export default function ExpenseReportDetailPage() {
  const searchParams = useSearchParams();
  const id = searchParams.get("id");
  const router = useRouter();
  const [report, setReport] = useState<Report | null>(null);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);
  const abortRef = useRef<AbortController | null>(null);

  const fetchReport = useCallback(async () => {
    if (!id) {
      setLoading(false);
      return;
    }
    abortRef.current?.abort();
    const controller = new AbortController();
    abortRef.current = controller;
    try {
      const { data: resp } = await api.get(`/tenant/expenses/reports/${id}`, { signal: controller.signal });
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
  }, [id]);

  useEffect(() => {
    void fetchReport();
    return () => { abortRef.current?.abort(); };
  }, [fetchReport]);

  const handleAction = async (action: string) => {
    if (!id) return;
    setActionLoading(true);
    try {
      await api.post(`/tenant/expenses/reports/${id}/${action}`);
      toast.success(`Report ${action} successful`);
      void fetchReport();
    } catch {
      toast.error(`Failed to ${action} report`);
    } finally {
      setActionLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="flex min-h-[200px] items-center justify-center gap-2 text-muted-foreground">
        <Loader2 className="size-6 animate-spin" />
      </div>
    );
  }

  if (!report) {
    return <p className="text-muted-foreground">Report not found.</p>;
  }

  const statusColors: Record<string, string> = {
    draft: "bg-gray-100 text-gray-700",
    submitted: "bg-yellow-100 text-yellow-700",
    approved: "bg-green-100 text-green-700",
    rejected: "bg-red-100 text-red-700",
    reimbursed: "bg-blue-100 text-blue-700",
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-3">
        <Button variant="outline" size="sm" onClick={() => router.push("/dashboard/modules/expenses/reports")}>
          <ArrowLeft className="size-4" />
        </Button>
        <div className="flex-1">
          <h1 className="text-xl font-semibold">{report.title}</h1>
          <p className="text-sm text-muted-foreground">{report.description}</p>
        </div>
        <span className={`rounded-full px-3 py-1 text-xs font-medium ${statusColors[report.status] ?? "bg-gray-100"}`}>
          {report.status}
        </span>
      </div>

      <div className="grid gap-4 sm:grid-cols-3">
        <div className="rounded-xl border bg-card p-4">
          <p className="text-sm text-muted-foreground">Total Amount</p>
          <p className="text-2xl font-semibold">${report.total_amount.toLocaleString()}</p>
        </div>
        <div className="rounded-xl border bg-card p-4">
          <p className="text-sm text-muted-foreground">Expenses</p>
          <p className="text-2xl font-semibold">{report.expenses?.length ?? 0}</p>
        </div>
        <div className="rounded-xl border bg-card p-4">
          <p className="text-sm text-muted-foreground">Created</p>
          <p className="text-lg font-medium">{new Date(report.created_at).toLocaleDateString()}</p>
        </div>
      </div>

      <div className="flex gap-2">
        {report.status === "draft" && (
          <Button size="sm" onClick={() => void handleAction("submit")} disabled={actionLoading}>
            <Send className="size-4" /> Submit
          </Button>
        )}
        {report.status === "submitted" && (
          <>
            <Button size="sm" variant="default" onClick={() => void handleAction("approve")} disabled={actionLoading}>
              <CheckCircle className="size-4" /> Approve
            </Button>
            <Button size="sm" variant="destructive" onClick={() => void handleAction("reject")} disabled={actionLoading}>
              <XCircle className="size-4" /> Reject
            </Button>
          </>
        )}
      </div>

      <div className="rounded-xl border">
        <div className="border-b px-4 py-3">
          <h2 className="font-semibold">Expenses in this report</h2>
        </div>
        <div className="divide-y">
          {report.expenses?.map((exp) => (
            <div key={exp.id} className="flex items-center justify-between px-4 py-3">
              <div>
                <p className="font-medium">{exp.title}</p>
                <p className="text-sm text-muted-foreground">{exp.date} • {exp.vendor || "No vendor"}</p>
              </div>
              <div className="text-right">
                <p className="font-semibold">${exp.amount.toLocaleString()} {exp.currency}</p>
                <span className={`text-xs ${statusColors[exp.status] ?? ""} rounded-full px-2 py-0.5`}>{exp.status}</span>
              </div>
            </div>
          )) ?? (
            <div className="px-4 py-6 text-center text-muted-foreground">No expenses in this report</div>
          )}
        </div>
      </div>
    </div>
  );
}
