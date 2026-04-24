"use client";

import { Building2 } from "lucide-react";
import { useI18n } from "@/context/i18n-context";
import { Card, CardContent } from "@/components/ui/card";

export default function CrmCompaniesPage() {
  const { t } = useI18n();
  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <div className="flex items-center gap-2">
          <Building2 className="size-5 text-muted-foreground" />
          <h1 className="text-xl font-semibold">{t("dashboard.crm.companies", "Companies")}</h1>
        </div>
        <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.crm.companies_subtitle", "Manage your CRM companies")}</p>
      </div>
      <Card>
        <CardContent className="flex min-h-[200px] items-center justify-center text-muted-foreground">
          {t("dashboard.coming_soon", "Coming soon — this page is under development.")}
        </CardContent>
      </Card>
    </div>
  );
}
