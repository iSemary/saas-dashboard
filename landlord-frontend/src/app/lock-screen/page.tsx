"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import { Loader2, Lock } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useAuth } from "@/context/auth-context";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { LoginShell } from "@/components/login-shell";

export default function LockScreenPage() {
  const router = useRouter();
  const { t } = useI18n();
  const { isAuthenticated, loading, user, logout } = useAuth();
  const [password, setPassword] = useState("");
  const [submitting, setSubmitting] = useState(false);

  useEffect(() => {
    if (!loading && !isAuthenticated) {
      router.replace("/login");
    }
  }, [loading, isAuthenticated, router]);

  const unlock = async () => {
    setSubmitting(true);
    try {
      await api.post("/auth/verify-password", { password });
      toast.success(t("dashboard.auth.lock_unlocked", "Unlocked."));
      router.replace("/dashboard");
    } catch {
      toast.error(t("dashboard.auth.lock_invalid_password", "Invalid password."));
    } finally {
      setSubmitting(false);
    }
  };

  const handleLogout = async () => {
    await logout();
  };

  if (loading || !isAuthenticated) {
    return (
      <LoginShell>
        <div className="flex flex-1 items-center justify-center bg-muted/40">
          <Loader2 className="size-8 animate-spin text-muted-foreground" />
        </div>
      </LoginShell>
    );
  }

  return (
    <LoginShell>
      <div className="flex flex-1 flex-col items-center justify-center px-4 py-10">
        <Card className="w-full max-w-md border-border/80 shadow-lg">
          <CardHeader className="text-center">
            <div className="mx-auto mb-2 flex size-12 items-center justify-center rounded-full bg-muted">
              <Lock className="size-6" />
            </div>
            <CardTitle>{t("dashboard.auth.lock_title", "Screen locked")}</CardTitle>
            <CardDescription>
              {user?.name
                ? t("dashboard.auth.lock_desc_user", `Enter your password to continue, ${user.name}.`)
                : t("dashboard.auth.lock_desc", "Enter your password to continue.")}
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            <div className="space-y-2">
              <Label htmlFor="lock-password">{t("dashboard.auth.password", "Password")}</Label>
              <Input
                id="lock-password"
                type="password"
                autoComplete="current-password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                onKeyDown={(e) => {
                  if (e.key === "Enter") void unlock();
                }}
              />
            </div>
            <Button type="button" className="w-full" disabled={submitting || !password.length} onClick={() => void unlock()}>
              {submitting ? <Loader2 className="size-4 animate-spin" /> : null}
              {t("dashboard.auth.unlock", "Unlock")}
            </Button>
            <div className="text-center">
              <button
                type="button"
                className="text-sm text-muted-foreground hover:text-foreground hover:underline"
                onClick={() => void handleLogout()}
              >
                {t("dashboard.auth.lock_sign_in_different", "Or sign in as a different user")}
              </button>
            </div>
          </CardContent>
        </Card>
      </div>
    </LoginShell>
  );
}
