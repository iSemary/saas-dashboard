"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

type ModuleOption = { id: number; name: string; slug: string };

export default function OnboardingSelectModulesPage() {
  const { t } = useI18n();
  const router = useRouter();
  const [modules, setModules] = useState<ModuleOption[]>([]);
  const [selected, setSelected] = useState<Set<number>>(new Set());
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);

  useState(() => {
    api.get("/modules")
      .then((res) => {
        const data = Array.isArray(res.data) ? res.data : [];
        setModules(data as ModuleOption[]);
      })
      .catch(() => setModules([]))
      .finally(() => setLoading(false));
  });

  const handleSubmit = async () => {
    setSubmitting(true);
    try {
      await api.post("/onboarding/select-modules", { module_ids: [...selected] });
      router.push("/dashboard/onboarding/create-brand");
    } catch {
      toast.error(t("dashboard.onboarding.select_modules_failed", "Could not save module selection."));
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <Card className="w-full max-w-lg border-border/80 shadow-lg">
        <CardHeader className="text-center">
          <CardTitle>{t("dashboard.onboarding.select_modules_title", "Select Modules")}</CardTitle>
          <CardDescription>
            {t("dashboard.onboarding.select_modules_desc", "Choose which modules to enable for your platform.")}
          </CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="max-h-64 space-y-2 overflow-y-auto rounded-md border border-border/80 p-3">
            {modules.map((m) => (
              <label key={m.id} className="flex cursor-pointer items-center gap-2 text-sm">
                <input
                  type="checkbox"
                  className="rounded border-input"
                  checked={selected.has(m.id)}
                  onChange={(e) => {
                    setSelected((prev) => {
                      const next = new Set(prev);
                      if (e.target.checked) next.add(m.id);
                      else next.delete(m.id);
                      return next;
                    });
                  }}
                />
                <span>{m.name}</span>
              </label>
            ))}
          </div>
          <Button type="button" className="w-full" disabled={submitting} onClick={() => void handleSubmit()}>
            {submitting ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.onboarding.continue", "Continue")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
