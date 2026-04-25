"use client";

import { Receipt } from "lucide-react";
import { ModulePageHeader } from "@/components/module-page-header";
import { Card, CardContent } from "@/components/ui/card";

export default function HrExpensesPage() {
  return (
    <div className="space-y-4">
      <ModulePageHeader
        icon={Receipt}
        titleKey="dashboard.hr.expenses"
        titleFallback="Expenses"
        subtitleKey="dashboard.hr.expenses_subtitle"
        subtitleFallback="Submit and manage employee expense claims"
        dashboardHref="/dashboard/modules/hr"
        moduleKey="hr"
      />
      <Card>
        <CardContent className="pt-4 text-sm text-muted-foreground">
          Expense category and claims APIs are available in the HR module.
        </CardContent>
      </Card>
    </div>
  );
}
