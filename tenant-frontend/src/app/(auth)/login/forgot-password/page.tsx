"use client";

import { FormEvent, useState } from "react";
import Link from "next/link";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useI18n } from "@/context/i18n-context";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function ForgotPasswordPage() {
  const { t } = useI18n();
  const [email, setEmail] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);
  const [sent, setSent] = useState(false);

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      await api.post("/auth/forgot-password", { email });
      setSent(true);
      toast.success(t("dashboard.auth.forgot_check_email", "Check your email for reset instructions."));
    } catch {
      const msg = t("dashboard.auth.forgot_send_error", "Could not send reset email. Try again later.");
      setError(msg);
      toast.error(msg);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <div className="mb-6 w-full max-w-md text-center">
        <h1 className="text-2xl font-semibold tracking-tight">{t("dashboard.auth.forgot_page_title", "Forgot password")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.auth.forgot_page_subtitle", "We'll email you a link to reset your password.")}
        </p>
      </div>
      <Card className="w-full max-w-md border-border/80 shadow-lg">
        <CardHeader>
          <CardTitle>{t("dashboard.auth.forgot_card_title", "Reset link")}</CardTitle>
          <CardDescription>{t("dashboard.auth.forgot_card_desc", "Enter the email you use for this account.")}</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          {sent ? (
            <Alert>
              <AlertTitle>{t("dashboard.auth.forgot_inbox_title", "Check your inbox")}</AlertTitle>
              <AlertDescription>
                {t("dashboard.auth.forgot_inbox_desc", "If an account exists for this email, we sent reset instructions. You can close this page or")}{" "}
                <Link href="/login" className="font-medium text-primary underline">
                  {t("dashboard.auth.forgot_return_login", "return to login")}
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
                  <AlertTitle>{t("dashboard.auth.forgot_request_failed", "Request failed")}</AlertTitle>
                  <AlertDescription>{error}</AlertDescription>
                </Alert>
              ) : null}
              <div className="space-y-2">
                <Label htmlFor="email">{t("dashboard.auth.email", "Email")}</Label>
                <Input
                  id="email"
                  type="email"
                  autoComplete="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder={t("dashboard.auth.placeholder_email", "you@example.com")}
                  disabled={submitting}
                  required
                />
              </div>
              <Button type="submit" className="w-full" disabled={submitting}>
                {submitting ? (
                  <>
                    <Loader2 className="size-4 animate-spin" />
                    {t("dashboard.auth.forgot_sending", "Sending…")}
                  </>
                ) : (
                  t("dashboard.auth.forgot_send_link", "Send reset link")
                )}
              </Button>
            </form>
          )}
          <p className="text-center text-sm text-muted-foreground">
            <Link href="/login" className="font-medium text-primary hover:underline">
              {t("dashboard.auth.back_to_login", "Back to login")}
            </Link>
          </p>
        </CardContent>
      </Card>
    </div>
  );
}
