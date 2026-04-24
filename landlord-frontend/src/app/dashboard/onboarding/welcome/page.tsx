"use client";

import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { useI18n } from "@/context/i18n-context";

export default function OnboardingWelcomePage() {
  const { t } = useI18n();
  const router = useRouter();

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <Card className="w-full max-w-lg border-border/80 shadow-lg">
        <CardHeader className="text-center">
          <CardTitle className="text-2xl">{t("dashboard.onboarding.welcome_title", "Welcome!")}</CardTitle>
          <CardDescription>
            {t("dashboard.onboarding.welcome_desc", "Let's set up your platform. This will only take a few minutes.")}
          </CardDescription>
        </CardHeader>
        <CardContent className="flex justify-center">
          <Button type="button" onClick={() => router.push("/dashboard/onboarding/select-plan")}>
            {t("dashboard.onboarding.get_started", "Get Started")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
