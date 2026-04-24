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

function ResetPasswordForm() {
  const { t } = useI18n();
  const router = useRouter();
  const searchParams = useSearchParams();
  const token = searchParams.get("token") ?? "";
  const email = searchParams.get("email") ?? "";

  const [password, setPassword] = useState("");
  const [passwordConfirmation, setPasswordConfirmation] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    if (!token || !email) {
      setError(
        t(
          "dashboard.auth.reset_invalid_params",
          "Invalid or expired reset link. Request a new one from the login page.",
        ),
      );
      return;
    }
    setSubmitting(true);
    try {
      await api.post("/auth/reset-password", {
        token,
        email,
        password,
        password_confirmation: passwordConfirmation,
      });
      toast.success(t("dashboard.auth.reset_success", "Password updated. You can sign in now."));
      router.replace("/login");
    } catch (err: unknown) {
      const msg =
        typeof err === "object" &&
        err !== null &&
        "response" in err &&
        typeof (err as { response?: { data?: { message?: string } } }).response?.data?.message === "string"
          ? String((err as { response: { data: { message: string } } }).response.data.message)
          : t("dashboard.auth.reset_generic_error", "Could not reset password. The link may have expired.");
      setError(msg);
      toast.error(msg);
    } finally {
      setSubmitting(false);
    }
  };

  const missingParams = !token || !email;

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <div className="mb-6 w-full max-w-md text-center">
        <h1 className="text-2xl font-semibold tracking-tight">{t("dashboard.auth.reset_page_heading", "Set a new password")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.auth.reset_page_subtitle", "Choose a strong password you haven't used elsewhere.")}
        </p>
      </div>
      <Card className="w-full max-w-md border-border/80 shadow-lg">
        <CardHeader>
          <CardTitle>{t("dashboard.auth.reset_card_title", "Reset password")}</CardTitle>
          <CardDescription>{t("dashboard.auth.reset_card_desc", "Enter the new password twice to confirm.")}</CardDescription>
        </CardHeader>
        <CardContent>
          {missingParams ? (
            <Alert variant="destructive">
              <AlertTitle>{t("dashboard.auth.reset_invalid_link_title", "Invalid link")}</AlertTitle>
              <AlertDescription>
                {t("dashboard.auth.reset_invalid_link_desc", "Open the reset link from your email, or")}{" "}
                <Link href="/login/forgot-password" className="underline">
                  {t("dashboard.auth.reset_request_new", "request a new link")}
                </Link>
                .
              </AlertDescription>
            </Alert>
          ) : (
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
                  <AlertTitle>{t("dashboard.auth.reset_could_not_title", "Could not reset")}</AlertTitle>
                  <AlertDescription>{error}</AlertDescription>
                </Alert>
              ) : null}
              <div className="space-y-2">
                <Label htmlFor="email">{t("dashboard.auth.email", "Email")}</Label>
                <Input id="email" type="email" value={email} disabled readOnly className="bg-muted/50" />
              </div>
              <div className="space-y-2">
                <Label htmlFor="password">{t("dashboard.auth.new_password", "New password")}</Label>
                <Input
                  id="password"
                  type="password"
                  autoComplete="new-password"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  placeholder="••••••••"
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
              <Button type="submit" className="w-full" disabled={submitting || missingParams}>
                {submitting ? (
                  <>
                    <Loader2 className="size-4 animate-spin" />
                    {t("dashboard.auth.reset_updating", "Updating…")}
                  </>
                ) : (
                  t("dashboard.auth.reset_update_button", "Update password")
                )}
              </Button>
            </form>
          )}
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

function ResetPasswordSuspenseFallback() {
  const { t } = useI18n();
  return (
    <div className="flex flex-1 flex-col items-center justify-center gap-2 py-20 text-muted-foreground">
      <Loader2 className="size-8 animate-spin" />
      <span className="text-sm">{t("dashboard.auth.page_loading", "Loading…")}</span>
    </div>
  );
}

export default function ResetPasswordPage() {
  return (
    <Suspense fallback={<ResetPasswordSuspenseFallback />}>
      <ResetPasswordForm />
    </Suspense>
  );
}
