"use client";

import { useEffect, useState } from "react";
import { Card, CardContent, CardDescription, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { toast } from "sonner";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from "@/components/ui/dialog";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Package, CheckCircle2, AlertCircle, ArrowLeft } from "lucide-react";
import Link from "next/link";
import {
  getAvailableModules,
  subscribeToModule,
  unsubscribeFromModule,
  previewModuleProration,
  type ModuleCatalogItem,
  type ProrationPreview,
} from "@/lib/billing";
import { formatCurrency } from "@/lib/utils";

const billingCycles = [
  { value: "monthly", label: "Monthly" },
  { value: "quarterly", label: "Quarterly" },
  { value: "annually", label: "Annually" },
];

export default function BillingModulesPage() {
  const [modules, setModules] = useState<ModuleCatalogItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [selectedModule, setSelectedModule] = useState<ModuleCatalogItem | null>(null);
  const [dialogOpen, setDialogOpen] = useState(false);
  const [billingCycle, setBillingCycle] = useState("monthly");
  const [proration, setProration] = useState<ProrationPreview | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const loadModules = async () => {
    try {
      setLoading(true);
      const data = await getAvailableModules();
      setModules(data);
    } catch (err) {
      console.error(err);
      toast.error("Failed to load modules");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadModules();
  }, []);

  const handleSubscribe = async () => {
    if (!selectedModule) return;

    try {
      setSubmitting(true);
      await subscribeToModule(selectedModule.id, billingCycle);
      toast.success(`Subscribed to ${selectedModule.name}`);
      setDialogOpen(false);
      loadModules();
    } catch (err) {
      console.error(err);
      toast.error("Failed to subscribe");
    } finally {
      setSubmitting(false);
    }
  };

  const handleUnsubscribe = async (module: ModuleCatalogItem) => {
    if (!confirm(`Are you sure you want to unsubscribe from ${module.name}?`)) return;

    try {
      await unsubscribeFromModule(module.id, false, false);
      toast.success(`Unsubscribed from ${module.name}`);
      loadModules();
    } catch (err) {
      console.error(err);
      toast.error("Failed to unsubscribe");
    }
  };

  const openSubscribeDialog = async (module: ModuleCatalogItem) => {
    setSelectedModule(module);
    setBillingCycle("monthly");
    
    try {
      const preview = await previewModuleProration(module.id, "monthly");
      setProration(preview);
    } catch {
      setProration(null);
    }
    
    setDialogOpen(true);
  };

  const handleBillingCycleChange = async (value: string | null) => {
    if (!value) return;
    setBillingCycle(value);
    if (selectedModule) {
      try {
        const preview = await previewModuleProration(selectedModule.id, value);
        setProration(preview);
      } catch {
        setProration(null);
      }
    }
  };

  if (loading) {
    return (
      <div className="space-y-4">
        <div className="flex items-center gap-4">
          <Skeleton className="h-10 w-32" />
        </div>
        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {[1, 2, 3].map((i) => (
            <Skeleton key={i} className="h-64" />
          ))}
        </div>
      </div>
    );
  }

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
        <h1 className="text-2xl font-semibold tracking-tight">Add-on Modules</h1>
        <p className="text-muted-foreground">Enhance your plan with additional modules</p>
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {modules.map((module) => (
          <Card key={module.id} className={module.is_subscribed ? "border-primary" : undefined}>
            <CardHeader>
              <div className="flex items-start justify-between">
                <div className="flex items-center gap-2">
                  <Package className="size-5 text-muted-foreground" />
                  <CardTitle className="text-lg">{module.name}</CardTitle>
                </div>
                {module.is_subscribed ? (
                  <Badge className="bg-green-100 text-green-800">
                    <CheckCircle2 className="mr-1 size-3" />
                    Active
                  </Badge>
                ) : null}
              </div>
              <CardDescription>{module.description || `Access the ${module.name} module`}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="text-2xl font-bold">
                {module.base_price ? (
                  <>
                    {formatCurrency(module.base_price, module.currency_code || "USD")}
                    <span className="text-sm font-normal text-muted-foreground"> / {module.billing_cycle}</span>
                  </>
                ) : (
                  <span className="text-muted-foreground">Contact sales</span>
                )}
              </div>

              {module.trial_days > 0 && !module.is_subscribed && (
                <div className="flex items-center gap-2 text-sm text-amber-600">
                  <AlertCircle className="size-4" />
                  <span>{module.trial_days}-day free trial</span>
                </div>
              )}

              {module.is_subscribed && module.subscription && (
                <div className="space-y-1 text-sm text-muted-foreground">
                  <p>
                    Renews: {module.subscription.current_period_end ? new Date(module.subscription.current_period_end).toLocaleDateString() : "N/A"}
                  </p>
                </div>
              )}
            </CardContent>
            <CardFooter>
              {module.is_subscribed ? (
                <Button 
                  variant="outline" 
                  className="w-full"
                  onClick={() => handleUnsubscribe(module)}
                >
                  Unsubscribe
                </Button>
              ) : (
                <Button 
                  className="w-full"
                  onClick={() => openSubscribeDialog(module)}
                  disabled={!module.base_price}
                >
                  Subscribe
                </Button>
              )}
            </CardFooter>
          </Card>
        ))}
      </div>

      <Dialog open={dialogOpen} onOpenChange={setDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Subscribe to {selectedModule?.name}</DialogTitle>
            <DialogDescription>
              Choose your billing cycle for this module
            </DialogDescription>
          </DialogHeader>

          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <Label>Billing Cycle</Label>
              <Select value={billingCycle} onValueChange={handleBillingCycleChange}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {billingCycles.map((cycle) => (
                    <SelectItem key={cycle.value} value={cycle.value}>
                      {cycle.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            {proration && proration.net_amount > 0 && (
              <div className="rounded-lg bg-muted p-4 space-y-2">
                <p className="font-medium">Prorated charge</p>
                <p className="text-sm text-muted-foreground">
                  You&apos;ll be charged {formatCurrency(proration.net_amount, "USD")} today for the remaining period.
                </p>
              </div>
            )}

            <div className="flex justify-between items-center pt-2">
              <span className="text-muted-foreground">Total</span>
              <span className="text-xl font-bold">
                {selectedModule?.base_price 
                  ? formatCurrency(selectedModule.base_price, selectedModule.currency_code || "USD")
                  : "-"} / month
              </span>
            </div>
          </div>

          <DialogFooter>
            <Button variant="outline" onClick={() => setDialogOpen(false)}>
              Cancel
            </Button>
            <Button onClick={handleSubscribe} disabled={submitting}>
              {submitting ? "Subscribing..." : "Confirm Subscription"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
