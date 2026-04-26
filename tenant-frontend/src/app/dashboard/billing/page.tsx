"use client";

import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { toast } from "sonner";
import { useRouter } from "next/navigation";
import { 
  CreditCard, 
  Receipt, 
  Package, 
  ArrowRight, 
  Calendar,
  CheckCircle2,
  AlertCircle,
  Wallet,
} from "lucide-react";
import { getBillingOverview, type BillingOverview } from "@/lib/billing";
import { formatCurrency } from "@/lib/utils";

export default function BillingOverviewPage() {
  const router = useRouter();
  const [loading, setLoading] = useState(true);
  const [billing, setBilling] = useState<BillingOverview | null>(null);

  const loadBilling = async () => {
    try {
      setLoading(true);
      const data = await getBillingOverview();
      setBilling(data);
    } catch (err) {
      console.error(err);
      toast.error("Failed to load billing information");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadBilling();
  }, []);

  if (loading) {
    return (
      <div className="space-y-6">
        <Skeleton className="h-10 w-48" />
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          <Skeleton className="h-48" />
          <Skeleton className="h-48" />
          <Skeleton className="h-48" />
        </div>
      </div>
    );
  }

  if (!billing) {
    return (
      <div className="flex h-[400px] items-center justify-center">
        <p className="text-muted-foreground">Failed to load billing information</p>
      </div>
    );
  }

  const getStatusBadge = (status: string) => {
    const variants: Record<string, { variant: "default" | "secondary" | "destructive" | "outline"; label: string }> = {
      active: { variant: "default", label: "Active" },
      trial: { variant: "secondary", label: "Trial" },
      past_due: { variant: "destructive", label: "Past Due" },
      canceled: { variant: "outline", label: "Canceled" },
      no_subscription: { variant: "outline", label: "No Subscription" },
    };
    const config = variants[status] || { variant: "outline", label: status };
    return <Badge variant={config.variant}>{config.label}</Badge>;
  };

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight">Billing & Subscription</h1>
          <p className="text-muted-foreground">Manage your plan, payment methods, and billing history</p>
        </div>
      </div>

      {/* Current Plan Card */}
      <Card>
        <CardHeader className="pb-3">
          <div className="flex items-center justify-between">
            <CardTitle className="flex items-center gap-2">
              <Package className="size-5" />
              Current Plan
            </CardTitle>
            {getStatusBadge(billing.payment_status)}
          </div>
          <CardDescription>Your current subscription details</CardDescription>
        </CardHeader>
        <CardContent>
          {billing.subscription ? (
            <div className="space-y-4">
              <div className="flex items-baseline gap-2">
                <span className="text-3xl font-bold">{billing.subscription.plan.name}</span>
                <span className="text-muted-foreground">
                  {formatCurrency(billing.subscription.price, billing.subscription.currency)} / {billing.subscription.billing_cycle}
                </span>
              </div>
              
              <div className="grid gap-2 text-sm">
                <div className="flex items-center gap-2">
                  <Calendar className="size-4 text-muted-foreground" />
                  <span>
                    Current period: {billing.subscription.current_period_start ? new Date(billing.subscription.current_period_start).toLocaleDateString() : "N/A"} 
                    {" - "}
                    {billing.subscription.current_period_end ? new Date(billing.subscription.current_period_end).toLocaleDateString() : "N/A"}
                  </span>
                </div>
                <div className="flex items-center gap-2">
                  <Receipt className="size-4 text-muted-foreground" />
                  <span>
                    Next billing: {billing.subscription.next_billing_at ? new Date(billing.subscription.next_billing_at).toLocaleDateString() : "N/A"}
                  </span>
                </div>
                {billing.subscription.trial_ends_at && (
                  <div className="flex items-center gap-2 text-amber-600">
                    <AlertCircle className="size-4" />
                    <span>Trial ends: {new Date(billing.subscription.trial_ends_at).toLocaleDateString()}</span>
                  </div>
                )}
              </div>

              <div className="flex gap-2 pt-2">
                <Button variant="outline" onClick={() => router.push("/dashboard/billing/plans")}>
                  Change Plan
                </Button>
                <Button variant="outline" onClick={() => router.push("/dashboard/billing/invoices")}>
                  View Invoices
                </Button>
              </div>
            </div>
          ) : (
            <div className="space-y-4">
              <p className="text-muted-foreground">You don&apos;t have an active subscription</p>
              <Button onClick={() => router.push("/dashboard/billing/plans")}>
                View Plans
              </Button>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Active Modules */}
      <Card>
        <CardHeader className="pb-3">
          <div className="flex items-center justify-between">
            <CardTitle className="flex items-center gap-2">
              <Package className="size-5" />
              Active Modules
            </CardTitle>
            <Badge variant="secondary">{billing.module_count} active</Badge>
          </div>
          <CardDescription>Your add-on modules</CardDescription>
        </CardHeader>
        <CardContent>
          {billing.active_modules.length > 0 ? (
            <div className="space-y-3">
              {billing.active_modules.map((module) => (
                <div key={module.id} className="flex items-center justify-between rounded-lg border p-3">
                  <div className="space-y-1">
                    <p className="font-medium">{module.module_key.toUpperCase()}</p>
                    <p className="text-sm text-muted-foreground">
                      {module.price ? formatCurrency(module.price, billing.currency) : "Free"} / {module.billing_cycle}
                    </p>
                  </div>
                  <Badge variant="outline">Active</Badge>
                </div>
              ))}
              <Button variant="outline" className="w-full" onClick={() => router.push("/dashboard/billing/modules")}>
                Manage Modules
                <ArrowRight className="ml-2 size-4" />
              </Button>
            </div>
          ) : (
            <div className="space-y-4">
              <p className="text-muted-foreground">No active add-on modules</p>
              <Button variant="outline" className="w-full" onClick={() => router.push("/dashboard/billing/modules")}>
                Browse Modules
                <ArrowRight className="ml-2 size-4" />
              </Button>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Upcoming Invoice */}
      {billing.upcoming_invoice && (
        <Card>
          <CardHeader className="pb-3">
            <CardTitle className="flex items-center gap-2">
              <Receipt className="size-5" />
              Upcoming Invoice
            </CardTitle>
            <CardDescription>Estimated charges for your next billing cycle</CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {billing.upcoming_invoice.line_items.map((item, idx) => (
                <div key={idx} className="flex justify-between text-sm">
                  <span className="text-muted-foreground">{item.description}</span>
                  <span>{formatCurrency(item.amount, billing.upcoming_invoice?.currency_code || billing.currency)}</span>
                </div>
              ))}
              <div className="border-t pt-4">
                <div className="flex justify-between text-sm">
                  <span className="text-muted-foreground">Subtotal</span>
                  <span>{formatCurrency(billing.upcoming_invoice.subtotal, billing.upcoming_invoice.currency_code)}</span>
                </div>
                {billing.upcoming_invoice.discount_amount > 0 && (
                  <div className="flex justify-between text-sm text-green-600">
                    <span>Discount</span>
                    <span>-{formatCurrency(billing.upcoming_invoice.discount_amount, billing.upcoming_invoice.currency_code)}</span>
                  </div>
                )}
                <div className="flex justify-between text-lg font-semibold">
                  <span>Total</span>
                  <span>{formatCurrency(billing.upcoming_invoice.total_amount, billing.upcoming_invoice.currency_code)}</span>
                </div>
                <p className="text-xs text-muted-foreground mt-2">
                  Due on {new Date(billing.upcoming_invoice.due_date).toLocaleDateString()}
                </p>
              </div>
            </div>
          </CardContent>
        </Card>
      )}

      {/* Payment Method */}
      <Card>
        <CardHeader className="pb-3">
          <div className="flex items-center justify-between">
            <CardTitle className="flex items-center gap-2">
              <CreditCard className="size-5" />
              Payment Method
            </CardTitle>
            {billing.billing_profile?.has_payment_method ? (
              <CheckCircle2 className="size-5 text-green-500" />
            ) : (
              <AlertCircle className="size-5 text-amber-500" />
            )}
          </div>
          <CardDescription>Your default payment method for automatic billing</CardDescription>
        </CardHeader>
        <CardContent>
          {billing.billing_profile?.has_payment_method ? (
            <div className="space-y-4">
              <div className="flex items-center gap-3">
                <Wallet className="size-8 text-muted-foreground" />
                <div>
                  <p className="font-medium">Default payment method on file</p>
                  <p className="text-sm text-muted-foreground">{billing.billing_profile.default_gateway}</p>
                </div>
              </div>
              <div className="flex gap-2">
                <Button variant="outline" onClick={() => router.push("/dashboard/billing/payment-methods")}>
                  Manage Payment Methods
                </Button>
              </div>
            </div>
          ) : (
            <div className="space-y-4">
              <div className="flex items-center gap-2 text-amber-600">
                <AlertCircle className="size-5" />
                <span>No payment method on file</span>
              </div>
              <Button onClick={() => router.push("/dashboard/billing/payment-methods")}>
                Add Payment Method
              </Button>
            </div>
          )}
        </CardContent>
      </Card>

      {/* Account Balance */}
      {billing.balance !== 0 && (
        <Card>
          <CardHeader className="pb-3">
            <CardTitle className="flex items-center gap-2">
              <Wallet className="size-5" />
              Account Balance
            </CardTitle>
          </CardHeader>
          <CardContent>
            <p className={`text-2xl font-bold ${billing.balance > 0 ? "text-green-600" : "text-red-600"}`}>
              {billing.balance > 0 ? "+" : ""}
              {formatCurrency(Math.abs(billing.balance), billing.currency)}
            </p>
            <p className="text-sm text-muted-foreground">
              {billing.balance > 0 ? "Credit available" : "Amount due"}
            </p>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
