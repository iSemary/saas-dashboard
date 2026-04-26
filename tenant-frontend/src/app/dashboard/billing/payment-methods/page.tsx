"use client";

import { useEffect, useState } from "react";
import { Card, CardContent, CardHeader, CardTitle, CardFooter } from "@/components/ui/card";
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
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { ArrowLeft, CreditCard, Trash2, Star, Plus, Wallet } from "lucide-react";
import Link from "next/link";
import {
  getPaymentMethods,
  createSetupIntent,
  setDefaultPaymentMethod,
  removePaymentMethod,
  attachPaymentMethod,
  type PaymentMethod,
} from "@/lib/billing";

const gateways = [
  { value: "stripe", label: "Credit Card (Stripe)" },
  { value: "paypal", label: "PayPal" },
];

export default function BillingPaymentMethodsPage() {
  const [methods, setMethods] = useState<PaymentMethod[]>([]);
  const [loading, setLoading] = useState(true);
  const [addDialogOpen, setAddDialogOpen] = useState(false);
  const [selectedGateway, setSelectedGateway] = useState("stripe");
  const [setupIntent, setSetupIntent] = useState<{ client_secret: string | null; customer_id: string | null } | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const loadMethods = async () => {
    try {
      setLoading(true);
      const data = await getPaymentMethods();
      setMethods(data);
    } catch (err) {
      console.error(err);
      toast.error("Failed to load payment methods");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    loadMethods();
  }, []);

  const handleAddClick = async () => {
    try {
      const intent = await createSetupIntent(selectedGateway);
      setSetupIntent(intent);
      setAddDialogOpen(true);
    } catch (err) {
      console.error(err);
      toast.error("Failed to initialize payment method setup");
    }
  };

  const handleSetDefault = async (id: number) => {
    try {
      await setDefaultPaymentMethod(id);
      toast.success("Default payment method updated");
      loadMethods();
    } catch (err) {
      console.error(err);
      toast.error("Failed to update default payment method");
    }
  };

  const handleRemove = async (id: number, isDefault: boolean) => {
    if (isDefault) {
      toast.error("Cannot remove default payment method. Set another as default first.");
      return;
    }

    if (!confirm("Are you sure you want to remove this payment method?")) return;

    try {
      await removePaymentMethod(id);
      toast.success("Payment method removed");
      loadMethods();
    } catch (err) {
      console.error(err);
      toast.error("Failed to remove payment method");
    }
  };

  const getCardBrandIcon = (brand: string | null) => {
    const icons: Record<string, string> = {
      visa: "VISA",
      mastercard: "MC",
      amex: "AMEX",
      discover: "DISC",
    };
    return icons[brand?.toLowerCase() || ""] || "CARD";
  };

  if (loading) {
    return (
      <div className="space-y-4">
        <Skeleton className="h-10 w-32" />
        <Skeleton className="h-48" />
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

      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-semibold tracking-tight">Payment Methods</h1>
          <p className="text-muted-foreground">Manage your payment methods for automatic billing</p>
        </div>
        <Button onClick={() => setAddDialogOpen(true)}>
          <Plus className="mr-2 size-4" />
          Add Payment Method
        </Button>
      </div>

      {methods.length === 0 ? (
        <Card>
          <CardContent className="flex flex-col items-center justify-center py-12">
            <Wallet className="size-12 text-muted-foreground mb-4" />
            <p className="text-muted-foreground mb-4">No payment methods on file</p>
            <Button onClick={() => setAddDialogOpen(true)}>
              <Plus className="mr-2 size-4" />
              Add Payment Method
            </Button>
          </CardContent>
        </Card>
      ) : (
        <div className="grid gap-4">
          {methods.map((method) => (
            <Card key={method.id} className={method.is_default ? "border-primary" : undefined}>
              <CardHeader className="pb-3">
                <div className="flex items-start justify-between">
                  <div className="flex items-center gap-3">
                    <div className="flex h-10 w-16 items-center justify-center rounded bg-muted text-sm font-bold">
                      {getCardBrandIcon(method.brand)}
                    </div>
                    <div>
                      <p className="font-medium">
                        {method.brand?.toUpperCase() || "Card"} ending in {method.last_four || "****"}
                      </p>
                      <p className="text-sm text-muted-foreground">
                        Expires {method.exp_month}/{method.exp_year}
                      </p>
                    </div>
                  </div>
                  {method.is_default && (
                    <Badge variant="default">
                      <Star className="mr-1 size-3" />
                      Default
                    </Badge>
                  )}
                </div>
              </CardHeader>
              <CardFooter className="flex gap-2 pt-0">
                {!method.is_default && (
                  <Button variant="outline" size="sm" onClick={() => handleSetDefault(method.id)}>
                    <Star className="mr-2 size-4" />
                    Set as Default
                  </Button>
                )}
                <Button
                  variant="outline"
                  size="sm"
                  className="text-destructive"
                  onClick={() => handleRemove(method.id, method.is_default)}
                >
                  <Trash2 className="mr-2 size-4" />
                  Remove
                </Button>
              </CardFooter>
            </Card>
          ))}
        </div>
      )}

      <Dialog open={addDialogOpen} onOpenChange={setAddDialogOpen}>
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Add Payment Method</DialogTitle>
            <DialogDescription>Choose your preferred payment method</DialogDescription>
          </DialogHeader>

          <div className="space-y-4 py-4">
            <div className="space-y-2">
              <label>Payment Method</label>
              <Select value={selectedGateway} onValueChange={(v) => { if (v != null) setSelectedGateway(v); }}>
                <SelectTrigger>
                  <SelectValue />
                </SelectTrigger>
                <SelectContent>
                  {gateways.map((gateway) => (
                    <SelectItem key={gateway.value} value={gateway.value}>
                      {gateway.label}
                    </SelectItem>
                  ))}
                </SelectContent>
              </Select>
            </div>

            <div className="rounded-lg bg-muted p-4">
              <p className="text-sm text-muted-foreground">
                You will be redirected to {selectedGateway === "stripe" ? "Stripe" : "PayPal"} to securely add your payment method.
              </p>
            </div>
          </div>

          <DialogFooter>
            <Button variant="outline" onClick={() => setAddDialogOpen(false)}>
              Cancel
            </Button>
            <Button onClick={handleAddClick} disabled={submitting}>
              {submitting ? "Setting up..." : "Continue"}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  );
}
