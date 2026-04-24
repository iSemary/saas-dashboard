"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function OnboardingSelectPlanPage() {
  const { t } = useI18n();
  const router = useRouter();
  const [planId, setPlanId] = useState<string>("");
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async () => {
    if (!planId) {
      toast.error(t("dashboard.onboarding.select_plan_error", "Please select a plan."));
      return;
    }
    setSubmitting(true);
    try {
      await api.post("/onboarding/select-plan", { plan_id: Number(planId) });
      router.push("/dashboard/onboarding/select-modules");
    } catch {
      toast.error(t("dashboard.onboarding.select_plan_failed", "Could not select plan."));
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <Card className="w-full max-w-lg border-border/80 shadow-lg">
        <CardHeader className="text-center">
          <CardTitle>{t("dashboard.onboarding.select_plan_title", "Select a Plan")}</CardTitle>
          <CardDescription>
            {t("dashboard.onboarding.select_plan_desc", "Choose the subscription plan that fits your needs.")}
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="plan-id">{t("dashboard.onboarding.plan_id", "Plan ID")}</Label>
            <Input id="plan-id" type="number" value={planId} onChange={(e) => setPlanId(e.target.value)} />
          </div>
          <Button type="button" className="w-full" disabled={submitting} onClick={() => void handleSubmit()}>
            {submitting ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.onboarding.continue", "Continue")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
