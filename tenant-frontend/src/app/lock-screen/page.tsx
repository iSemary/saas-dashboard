"use client";

import { FormEvent, useState } from "react";
import { useRouter } from "next/navigation";
import { Loader2, Lock } from "lucide-react";
import { toast } from "sonner";
import { useAuth } from "@/context/auth-context";
import { useI18n } from "@/context/i18n-context";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Blur } from "@/components/animate-ui/primitives/effects/blur";
import { Fade } from "@/components/animate-ui/primitives/effects/fade";
import { TypingText, TypingTextCursor } from "@/components/animate-ui/primitives/texts/typing";

export default function LockScreenPage() {
  const { t } = useI18n();
  const { user, login } = useAuth();
  const router = useRouter();
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [submitting, setSubmitting] = useState(false);

  const onSubmit = async (e: FormEvent) => {
    e.preventDefault();
    setError(null);
    setSubmitting(true);
    try {
      await login({ email: user?.email ?? "", password });
      toast.success(t("dashboard.auth.unlocked", "Unlocked successfully."));
      router.push("/dashboard");
    } catch {
      const msg = t("dashboard.auth.invalid_credentials", "Invalid credentials.");
      setError(msg);
      toast.error(msg);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
      <div className="mb-6 flex flex-col items-center gap-3">
        <div className="flex size-20 items-center justify-center rounded-full bg-muted">
          <Lock className="size-10 text-muted-foreground" />
        </div>
        <h1 className="text-2xl font-semibold tracking-tight">
          <TypingText
            text={t("dashboard.auth.locked", "Screen locked")}
            inView
            inViewOnce
            duration={60}
          />
          <TypingTextCursor />
        </h1>
        <Fade delay={400}>
          <p className="text-sm text-muted-foreground">
            {t("dashboard.auth.lock_subtitle", `Enter your password to unlock, ${user?.name ?? "User"}.`)}
          </p>
        </Fade>
      </div>
      <Blur inView inViewOnce delay={200}>
        <Card className="w-full max-w-lg border-border/80 shadow-lg">
        <CardHeader>
          <CardTitle>{t("dashboard.auth.unlock", "Unlock")}</CardTitle>
          <CardDescription>{t("dashboard.auth.unlock_desc", "Enter your password to continue.")}</CardDescription>
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
                <AlertTitle>{t("dashboard.auth.could_not_unlock", "Could not unlock")}</AlertTitle>
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            ) : null}
            <div className="space-y-2">
              <Label htmlFor="password">{t("dashboard.auth.password", "Password")}</Label>
              <Input
                id="password"
                type="password"
                autoComplete="current-password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder={t("dashboard.auth.placeholder_password_dots", "••••••••")}
                disabled={submitting}
                required
                onKeyDown={(e) => {
                  if (e.key === "Enter" && !submitting) {
                    e.preventDefault();
                    void onSubmit(e);
                  }
                }}
              />
            </div>
            <Button type="submit" className="w-full" disabled={submitting}>
              {submitting ? (
                <>
                  <Loader2 className="size-4 animate-spin" />
                  {t("dashboard.auth.unlocking", "Unlocking…")}
                </>
              ) : (
                t("dashboard.auth.unlock", "Unlock")
              )}
            </Button>
          </form>
        </CardContent>
        </Card>
      </Blur>
    </div>
  );
}
