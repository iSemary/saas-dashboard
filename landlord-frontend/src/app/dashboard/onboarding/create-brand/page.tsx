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
import { SlugInput } from "@/components/ui/slug-input";

export default function OnboardingCreateBrandPage() {
  const { t } = useI18n();
  const router = useRouter();
  const [form, setForm] = useState({ name: "", slug: "", domain: "" });
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async () => {
    if (!form.name.trim()) {
      toast.error(t("dashboard.onboarding.brand_name_required", "Brand name is required."));
      return;
    }
    setSubmitting(true);
    try {
      await api.post("/onboarding/create-brand", form);
      router.push("/dashboard/onboarding/complete");
    } catch {
      toast.error(t("dashboard.onboarding.create_brand_failed", "Could not create brand."));
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <Card className="w-full max-w-lg border-border/80 shadow-lg">
        <CardHeader className="text-center">
          <CardTitle>{t("dashboard.onboarding.create_brand_title", "Create Your Brand")}</CardTitle>
          <CardDescription>
            {t("dashboard.onboarding.create_brand_desc", "Set up your brand name and domain.")}
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label htmlFor="brand-name">{t("dashboard.users.col_name", "Name")}</Label>
            <Input id="brand-name" value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} />
          </div>
          <div className="space-y-2">
            <Label htmlFor="brand-slug">{t("dashboard.brands.slug", "Slug")}</Label>
            <SlugInput id="brand-slug" value={form.slug} onChange={(v) => setForm((f) => ({ ...f, slug: v }))} sourceValue={form.name} />
          </div>
          <div className="space-y-2">
            <Label htmlFor="brand-domain">{t("dashboard.tenants.domain", "Domain")}</Label>
            <Input id="brand-domain" value={form.domain} onChange={(e) => setForm((f) => ({ ...f, domain: e.target.value }))} />
          </div>
          <Button type="button" className="w-full" disabled={submitting} onClick={() => void handleSubmit()}>
            {submitting ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.onboarding.continue", "Continue")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
