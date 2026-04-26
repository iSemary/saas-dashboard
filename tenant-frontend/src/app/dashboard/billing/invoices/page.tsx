"use client";

import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { toast } from "sonner";
import { DataTable } from "@/components/data-table";
import type { ColumnDef } from "@tanstack/react-table";
import { ArrowLeft, Download, FileText, Loader2, CreditCard } from "lucide-react";
import Link from "next/link";
import { getInvoiceHistory, downloadInvoicePdf, payInvoice, type Invoice } from "@/lib/billing";
import { formatCurrency } from "@/lib/utils";
import { useI18n } from "@/context/i18n-context";

export default function BillingInvoicesPage() {
  const { t } = useI18n();
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState(true);
  const [payingId, setPayingId] = useState<number | null>(null);
  const [meta, setMeta] = useState({ current_page: 1, last_page: 1, per_page: 20, total: 0 });

  const loadInvoices = async (page = 1) => {
    try {
      setLoading(true);
      const res = await getInvoiceHistory({ page, per_page: 20 });
      setInvoices(res.data || []);
      setMeta(res.meta || meta);
    } catch (err) {
      console.error(err);
      toast.error("Failed to load invoices");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadInvoices();
  }, []);

  const handleDownload = async (invoice: Invoice) => {
    try {
      const blob = await downloadInvoicePdf(invoice.id);
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = `invoice-${invoice.invoice_number}.pdf`;
      a.click();
      window.URL.revokeObjectURL(url);
    } catch (err) {
      console.error(err);
      toast.error("Download failed");
    }
  };

  const handlePay = async (invoice: Invoice) => {
    try {
      setPayingId(invoice.id);
      await payInvoice(invoice.id);
      toast.success("Payment successful");
      loadInvoices();
    } catch (err) {
      console.error(err);
      toast.error("Payment failed");
    } finally {
      setPayingId(null);
    }
  };

  const getStatusBadge = (status: string) => {
    const variants: Record<string, { variant: "default" | "secondary" | "destructive" | "outline"; label: string }> = {
      paid: { variant: "default", label: "Paid" },
      pending: { variant: "secondary", label: "Pending" },
      overdue: { variant: "destructive", label: "Overdue" },
      void: { variant: "outline", label: "Void" },
      draft: { variant: "outline", label: "Draft" },
    };
    const config = variants[status] || { variant: "outline", label: status };
    return <Badge variant={config.variant}>{config.label}</Badge>;
  };

  const columns: ColumnDef<Invoice>[] = [
    { accessorKey: "invoice_number", header: "Invoice #" },
    {
      accessorKey: "status",
      header: "Status",
      cell: ({ row }) => getStatusBadge(row.original.status),
    },
    {
      accessorKey: "total_amount",
      header: "Amount",
      cell: ({ row }) => formatCurrency(row.original.total_amount, row.original.currency?.code),
    },
    {
      accessorKey: "invoice_date",
      header: "Date",
      cell: ({ row }) => new Date(row.original.invoice_date).toLocaleDateString(),
    },
    {
      accessorKey: "due_date",
      header: "Due Date",
      cell: ({ row }) => new Date(row.original.due_date).toLocaleDateString(),
    },
    {
      id: "actions",
      header: "",
      cell: ({ row }) => (
        <div className="flex gap-2">
          <Button variant="ghost" size="sm" onClick={() => handleDownload(row.original)}>
            <Download className="size-4" />
          </Button>
          {(row.original.status === "pending" || row.original.status === "overdue") && (
            <Button
              variant="ghost"
              size="sm"
              onClick={() => handlePay(row.original)}
              disabled={payingId === row.original.id}
            >
              {payingId === row.original.id ? (
                <Loader2 className="size-4 animate-spin" />
              ) : (
                <CreditCard className="size-4" />
              )}
            </Button>
          )}
        </div>
      ),
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
        <h1 className="text-2xl font-semibold tracking-tight">Invoices</h1>
        <p className="text-muted-foreground">View and download your billing history</p>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <FileText className="size-5" />
            Invoice History
          </CardTitle>
        </CardHeader>
        <CardContent>
          {loading ? (
            <Skeleton className="h-96" />
          ) : (
            <DataTable
              columns={columns}
              data={invoices}
              meta={meta}
              serverSide
              onTableChange={({ page }) => loadInvoices(page)}
            />
          )}
        </CardContent>
      </Card>
    </div>
  );
}
