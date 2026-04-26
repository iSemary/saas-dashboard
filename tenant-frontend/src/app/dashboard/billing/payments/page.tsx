"use client";

import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import type { ColumnDef } from "@tanstack/react-table";
import { ArrowLeft, CreditCard, Loader2, RefreshCw } from "lucide-react";
import Link from "next/link";
import { getPaymentHistory, retryPayment, type Payment } from "@/lib/billing";
import { formatCurrency } from "@/lib/utils";

export default function BillingPaymentsPage() {
  const [payments, setPayments] = useState<Payment[]>([]);
  const [loading, setLoading] = useState(true);
  const [retryingId, setRetryingId] = useState<number | null>(null);
  const [meta, setMeta] = useState({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

  const loadPayments = async (page = 1) => {
    try {
      setLoading(true);
      const res = await getPaymentHistory({ page, per_page: 20 });
      setPayments(res.data || []);
      setMeta(res.meta || meta);
    } catch (err) {
      console.error(err);
      toast.error("Failed to load payments");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadPayments();
  }, []);

  const handleRetry = async (payment: Payment) => {
    try {
      setRetryingId(payment.id);
      await retryPayment(payment.id);
      toast.success("Payment retry initiated");
      loadPayments();
    } catch (err) {
      console.error(err);
      toast.error("Payment retry failed");
    } finally {
      setRetryingId(null);
    }
  };

  const getStatusBadge = (status: string) => {
    const variants: Record<string, { variant: "default" | "secondary" | "destructive" | "outline"; label: string }> = {
      completed: { variant: "default", label: "Completed" },
      pending: { variant: "secondary", label: "Pending" },
      failed: { variant: "destructive", label: "Failed" },
      processing: { variant: "outline", label: "Processing" },
      refunded: { variant: "outline", label: "Refunded" },
    };
    const config = variants[status] || { variant: "outline", label: status };
    return <Badge variant={config.variant}>{config.label}</Badge>;
  };

  const columns: ColumnDef<Payment>[] = [
    { accessorKey: "payment_id", header: "Payment ID" },
    {
      accessorKey: "status",
      header: "Status",
      cell: ({ row }) => getStatusBadge(row.original.status),
    },
    {
      accessorKey: "amount",
      header: "Amount",
      cell: ({ row }) => formatCurrency(row.original.amount, row.original.currency?.code),
    },
    {
      accessorKey: "gateway",
      header: "Gateway",
      cell: ({ row }) => row.original.gateway?.toUpperCase() || "-",
    },
    {
      accessorKey: "attempted_at",
      header: "Date",
      cell: ({ row }) => new Date(row.original.attempted_at).toLocaleString(),
    },
    {
      id: "actions",
      header: "",
      cell: ({ row }) =>
        row.original.status === "failed" ? (
          <Button
            variant="ghost"
            size="sm"
            onClick={() => handleRetry(row.original)}
            disabled={retryingId === row.original.id}
          >
            {retryingId === row.original.id ? (
              <Loader2 className="size-4 animate-spin" />
            ) : (
              <RefreshCw className="size-4" />
            )}
          </Button>
        ) : null,
    },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center gap-4">
        <Link href="/dashboard/billing">
          <Button variant="outline" size="sm">
            <ArrowLeft className="mr-2 size-4" />
            Back to Billing
          </Button>
        </Link>
      </div>

      <div>
        <h1 className="text-2xl font-semibold tracking-tight">Payment History</h1>
        <p className="text-muted-foreground">View all payment attempts and transactions</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <CreditCard className="size-5" />
            Payments
          </CardTitle>
        </CardHeader>
        <CardContent>
          {loading ? (
            <Skeleton className="h-96" />
          ) : (
            <DataTable
              columns={columns}
              data={payments}
              meta={meta}
              serverSide
              onTableChange={({ page }) => loadPayments(page)}
            />
          )}
        </CardContent>
      </Card>
    </div>
  );
}
