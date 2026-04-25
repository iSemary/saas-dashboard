"use client";

import { FormEvent, Suspense, useState } from "react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function ResetPasswordPage() {
  return (
    <Suspense fallback={<div className="flex flex-1 items-center justify-center"><Loader2 className="size-6 animate-spin" /></div>}>
      <ResetPasswordForm />
    </Suspense>
  );
}

function ResetPasswordForm() {
  const { t } = useI18n();
  const router = useRouter();
  const searchParams = useSearchParams();
  const [password, setPassword] = useState("");
  const [passwordConfirmation, setPasswordConfirmation] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const token = searchParams.get("token") ?? "";
  const email = searchParams.get("email") ?? "";

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    if (password !== passwordConfirmation) {
      const msg = t("dashboard.auth.password_mismatch", "Passwords do not match.");
      setError(msg);
      return;
    }
    setSubmitting(true);
    try {
      await api.post("/tenant/auth/reset-password", { token, email, password, password_confirmation: passwordConfirmation });
      toast.success(t("dashboard.auth.password_reset_success", "Password reset successfully. You can now sign in."));
      router.push("/login");
    } catch {
      const msg = t("dashboard.auth.password_reset_error", "Failed to reset password. The link may have expired.");
      setError(msg);
      toast.error(msg);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <div className="mb-6 w-full max-w-md text-center">
        <h1 className="text-2xl font-semibold tracking-tight">{t("dashboard.auth.reset_page_title", "Reset password")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.auth.reset_page_subtitle", "Choose a new password for your account.")}
        </p>
      </div>
      <Card className="w-full max-w-md border-border/80 shadow-lg">
        <CardHeader>
          <CardTitle>{t("dashboard.auth.reset_card_title", "New password")}</CardTitle>
          <CardDescription>{t("dashboard.auth.reset_card_desc", "Enter and confirm your new password.")}</CardDescription>
        </CardHeader>
        <CardContent>
          <form
            className="space-y-4"
            method="post"
            onSubmit={(e) => {
              e.preventDefault();
              e.stopPropagation();
              void onSubmit(e);
            }}
          >
            {error ? (
              <Alert variant="destructive">
                <AlertTitle>{t("dashboard.auth.reset_failed", "Reset failed")}</AlertTitle>
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            ) : null}
            <div className="space-y-2">
              <Label htmlFor="password">{t("dashboard.auth.new_password", "New password")}</Label>
              <Input
                id="password"
                type="password"
                autoComplete="new-password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder={t("dashboard.auth.placeholder_password_dots", "••••••••")}
                disabled={submitting}
                required
                minLength={8}
              />
            </div>
            <div className="space-y-2">
              <Label htmlFor="password_confirmation">{t("dashboard.auth.confirm_password", "Confirm password")}</Label>
              <Input
                id="password_confirmation"
                type="password"
                autoComplete="new-password"
                value={passwordConfirmation}
                onChange={(e) => setPasswordConfirmation(e.target.value)}
                placeholder={t("dashboard.auth.placeholder_password_dots", "••••••••")}
                disabled={submitting}
                required
                minLength={8}
              />
            </div>
            <Button type="submit" className="w-full" disabled={submitting}>
              {submitting ? (
                <>
                  <Loader2 className="size-4 animate-spin" />
                  {t("dashboard.auth.resetting", "Resetting…")}
                </>
              ) : (
                t("dashboard.auth.reset_password", "Reset password")
              )}
            </Button>
          </form>
          <p className="mt-4 text-center text-sm text-muted-foreground">
            <Link href="/login" className="font-medium text-primary hover:underline">
              {t("dashboard.auth.back_to_login", "Back to login")}
            </Link>
          </p>
        </CardContent>
      </Card>
    </div>
  );
}
