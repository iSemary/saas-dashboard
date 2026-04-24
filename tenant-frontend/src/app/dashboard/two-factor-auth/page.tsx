"use client";

import { useState } from "react";
import { Loader2, Shield, ShieldOff } from "lucide-react";
import { toast } from "sonner";
import { useI18n } from "@/context/i18n-context";
import { setup2fa, confirm2fa, disable2fa } from "@/lib/tenant-resources";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";

export default function TwoFactorAuthPage() {
  const { t } = useI18n();
  const [qrCode, setQrCode] = useState<string | null>(null);
  const [secret, setSecret] = useState<string | null>(null);
  const [code, setCode] = useState("");
  const [loading, setLoading] = useState(false);

  const handleSetup = async () => {
    setLoading(true);
    try {
      const data = await setup2fa() as { secret: string; qr_code: string };
      setQrCode(data.qr_code);
      setSecret(data.secret);
      toast.success(t("dashboard.2fa.setup_success", "Scan the QR code with your authenticator app."));
    } catch {
      toast.error(t("dashboard.2fa.setup_error", "Failed to setup 2FA."));
    } finally {
      setLoading(false);
    }
  };

  const handleConfirm = async () => {
    setLoading(true);
    try {
      await confirm2fa(code, secret!);
      setQrCode(null); setSecret(null); setCode("");
      toast.success(t("dashboard.2fa.enabled", "2FA enabled successfully."));
    } catch {
      toast.error(t("dashboard.2fa.invalid_code", "Invalid code."));
    } finally {
      setLoading(false);
    }
  };

  const handleDisable = async () => {
    setLoading(true);
    try {
      await disable2fa();
      toast.success(t("dashboard.2fa.disabled", "2FA disabled."));
    } catch {
      toast.error(t("dashboard.2fa.disable_error", "Failed to disable 2FA."));
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.2fa.title", "Two-Factor Authentication")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.2fa.subtitle", "Secure your account with 2FA")}</p>
      </div>
      <Card>
        <CardHeader>
          <CardTitle>{t("dashboard.2fa.setup", "Setup 2FA")}</CardTitle>
          <CardDescription>{t("dashboard.2fa.setup_desc", "Enable two-factor authentication for extra security.")}</CardDescription>
        </CardHeader>
        <CardContent className="space-y-4">
          {!qrCode ? (
            <Button type="button" disabled={loading} onClick={() => void handleSetup()}>
              {loading ? <Loader2 className="size-4 animate-spin" /> : <Shield className="size-4" />}
              {t("dashboard.2fa.enable", "Enable 2FA")}
            </Button>
          ) : (
            <div className="space-y-4">
              {qrCode && <img src={qrCode} alt="2FA QR Code" className="mx-auto size-48" />}
              {secret && <p className="text-center text-sm text-muted-foreground">Secret: <code className="font-mono">{secret}</code></p>}
              <div className="space-y-2">
                <Label>{t("dashboard.2fa.code", "Verification Code")}</Label>
                <Input value={code} onChange={(e) => setCode(e.target.value)} placeholder="123456" />
              </div>
              <Button type="button" disabled={loading || !code} onClick={() => void handleConfirm()}>
                {loading ? <Loader2 className="size-4 animate-spin" /> : t("dashboard.2fa.confirm", "Confirm & Enable")}
              </Button>
            </div>
          )}
        </CardContent>
      </Card>
      <Card>
        <CardHeader>
          <CardTitle>{t("dashboard.2fa.disable_title", "Disable 2FA")}</CardTitle>
        </CardHeader>
        <CardContent>
          <Button type="button" variant="destructive" disabled={loading} onClick={() => void handleDisable()}>
            <ShieldOff className="size-4" />
            {t("dashboard.2fa.disable", "Disable 2FA")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
