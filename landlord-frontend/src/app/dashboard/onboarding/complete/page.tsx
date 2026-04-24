"use client";

import Link from "next/link";
import { CheckCircle2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { useI18n } from "@/context/i18n-context";

export default function OnboardingCompletePage() {
  const { t } = useI18n();

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <Card className="w-full max-w-lg border-border/80 shadow-lg">
        <CardHeader className="text-center">
          <div className="mx-auto mb-2 flex size-12 items-center justify-center rounded-full bg-green-100">
            <CheckCircle2 className="size-6 text-green-600" />
          </div>
          <CardTitle className="text-2xl">{t("dashboard.onboarding.complete_title", "All Done!")}</CardTitle>
          <CardDescription>
            {t("dashboard.onboarding.complete_desc", "Your platform is ready. Start exploring the dashboard.")}
          </CardDescription>
        </CardHeader>
        <CardContent className="flex justify-center">
          <Link href="/dashboard">
            <Button type="button">
              {t("dashboard.onboarding.go_to_dashboard", "Go to Dashboard")}
            </Button>
          </Link>
        </CardContent>
      </Card>
    </div>
  );
}
