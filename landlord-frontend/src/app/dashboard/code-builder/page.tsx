"use client";

import { useState } from "react";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function CodeBuilderPage() {
  const { t } = useI18n();
  const [submitting, setSubmitting] = useState(false);
  const [form, setForm] = useState({
    module_name: "",
    module_type: "landlord",
  });

  const handleBuild = async () => {
    if (!form.module_name.trim()) {
      toast.error(t("dashboard.code_builder.name_required", "Module name is required."));
      return;
    }
    setSubmitting(true);
    try {
      await api.post("/code-builder", form);
      toast.success(t("dashboard.code_builder.toast_built", "Module built successfully."));
    } catch {
      toast.error(t("dashboard.code_builder.toast_build_error", "Build failed."));
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.code_builder.title", "Code Builder")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.code_builder.subtitle", "Generate module scaffolding code.")}
        </p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>{t("dashboard.code_builder.form_title", "Build Module")}</CardTitle>
          <CardDescription>{t("dashboard.code_builder.form_desc", "Enter module details and generate code.")}</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="cb-name">{t("dashboard.code_builder.module_name", "Module Name")}</Label>
            <Input
              id="cb-name"
              value={form.module_name}
              onChange={(e) => setForm((f) => ({ ...f, module_name: e.target.value }))}
              placeholder={t("dashboard.code_builder.placeholder_name", "e.g. Blog")}
            />
          </div>
          <div className="space-y-2">
            <Label htmlFor="cb-type">{t("dashboard.code_builder.module_type", "Module Type")}</Label>
            <select
              id="cb-type"
              className="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1 text-sm"
              value={form.module_type}
              onChange={(e) => setForm((f) => ({ ...f, module_type: e.target.value }))}
            >
              <option value="landlord">Landlord</option>
              <option value="tenant">Tenant</option>
            </select>
          </div>
          <Button type="button" disabled={submitting} onClick={() => void handleBuild()}>
            {submitting ? <Loader2 className="size-4 animate-spin" /> : null}
            {t("dashboard.code_builder.build", "Build")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
