"use client";

import { FormEvent, useState } from "react";
import Link from "next/link";
import { useRouter } from "next/navigation";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import { useAuth } from "@/context/auth-context";
import { useI18n } from "@/context/i18n-context";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function VerifyTwoFactorPage() {
  const { t } = useI18n();
  const { verifyTwoFactor } = useAuth();
  const router = useRouter();
  const [code, setCode] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    const tempToken = window.localStorage.getItem("temp_token");
    if (!tempToken) {
      const msg = t("dashboard.auth.twofa_missing_token", "Missing temporary token. Start again from login.");
      setError(msg);
      toast.error(msg);
      return;
    }
    setSubmitting(true);
    try {
      await verifyTwoFactor(tempToken, code);
      window.localStorage.removeItem("temp_token");
      toast.success(t("dashboard.auth.login_signed_in", "Signed in successfully."));
      router.push("/dashboard");
    } catch {
      const msg = t("dashboard.auth.twofa_invalid_code", "Invalid verification code.");
      setError(msg);
      toast.error(msg);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <div className="mb-6 w-full max-w-md text-center">
        <h1 className="text-2xl font-semibold tracking-tight">{t("dashboard.auth.twofa_page_heading", "Two-factor verification")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.auth.twofa_page_subtitle", "Enter the code from your authenticator app.")}
        </p>
      </div>
      <Card className="w-full max-w-md border-border/80 shadow-lg">
        <CardHeader>
          <CardTitle>{t("dashboard.auth.twofa_card_title", "Verify 2FA")}</CardTitle>
          <CardDescription>{t("dashboard.auth.twofa_card_desc", "One-time password is required to finish signing in.")}</CardDescription>
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
                <AlertTitle>{t("dashboard.auth.twofa_alert_failed", "Verification failed")}</AlertTitle>
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            ) : null}
            <div className="space-y-2">
              <Label htmlFor="code">{t("dashboard.auth.twofa_code_label", "Code")}</Label>
              <Input
                id="code"
                inputMode="numeric"
                autoComplete="one-time-code"
                value={code}
                onChange={(e) => setCode(e.target.value)}
                placeholder={t("dashboard.auth.twofa_placeholder", "123456")}
                disabled={submitting}
                required
              />
            </div>
            <Button type="submit" className="w-full" disabled={submitting}>
              {submitting ? (
                <>
                  <Loader2 className="size-4 animate-spin" />
                  {t("dashboard.auth.twofa_verifying", "Verifying…")}
                </>
              ) : (
                t("dashboard.auth.twofa_verify", "Verify")
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
