"use client";

import { useState } from "react";
import { Loader2 } from "lucide-react";
import { toast } from "sonner";
import api from "@/lib/api";
import { useAuth } from "@/context/auth-context";
import { useI18n } from "@/context/i18n-context";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";

export default function ProfilePage() {
  const { t } = useI18n();
  const { user, refreshUser } = useAuth();

  const [saving, setSaving] = useState(false);
  const [generalForm, setGeneralForm] = useState({
    name: user?.name ?? "",
    email: user?.email ?? "",
  });
  const [securityForm, setSecurityForm] = useState({
    current_password: "",
    password: "",
    password_confirmation: "",
  });

  const saveGeneral = async () => {
    setSaving(true);
    try {
      await api.put("/profile", { type: "general", ...generalForm });
      await refreshUser();
      toast.success(t("dashboard.profile.toast_updated", "Profile updated."));
    } catch {
      toast.error(t("dashboard.profile.toast_save_error", "Could not update profile."));
    } finally {
      setSaving(false);
    }
  };

  const saveSecurity = async () => {
    if (securityForm.password !== securityForm.password_confirmation) {
      toast.error(t("dashboard.profile.password_mismatch", "Passwords do not match."));
      return;
    }
    setSaving(true);
    try {
      await api.put("/profile", { type: "security", ...securityForm });
      setSecurityForm({ current_password: "", password: "", password_confirmation: "" });
      toast.success(t("dashboard.profile.toast_password_updated", "Password updated."));
    } catch {
      toast.error(t("dashboard.profile.toast_password_error", "Could not update password."));
    } finally {
      setSaving(false);
    }
  };

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.profile.title", "My Account")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">
          {t("dashboard.profile.subtitle", "Manage your profile and security settings.")}
        </p>
      </div>

      <Tabs defaultValue="general">
        <TabsList>
          <TabsTrigger value="general">{t("dashboard.profile.tab_general", "General")}</TabsTrigger>
          <TabsTrigger value="security">{t("dashboard.profile.tab_security", "Security")}</TabsTrigger>
        </TabsList>

        <TabsContent value="general" className="mt-4">
          <Card>
            <CardHeader>
              <CardTitle>{t("dashboard.profile.general_title", "General Information")}</CardTitle>
              <CardDescription>{t("dashboard.profile.general_desc", "Update your name and email.")}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="profile-name">{t("dashboard.users.col_name", "Name")}</Label>
                <Input
                  id="profile-name"
                  value={generalForm.name}
                  onChange={(e) => setGeneralForm((f) => ({ ...f, name: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="profile-email">{t("dashboard.users.col_email", "Email")}</Label>
                <Input
                  id="profile-email"
                  type="email"
                  value={generalForm.email}
                  onChange={(e) => setGeneralForm((f) => ({ ...f, email: e.target.value }))}
                />
              </div>
              <Button type="button" disabled={saving} onClick={() => void saveGeneral()}>
                {saving ? <Loader2 className="size-4 animate-spin" /> : null}
                {t("dashboard.profile.save", "Save")}
              </Button>
            </CardContent>
          </Card>
        </TabsContent>

        <TabsContent value="security" className="mt-4">
          <Card>
            <CardHeader>
              <CardTitle>{t("dashboard.profile.security_title", "Change Password")}</CardTitle>
              <CardDescription>{t("dashboard.profile.security_desc", "Update your password for security.")}</CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="current-password">{t("dashboard.profile.current_password", "Current Password")}</Label>
                <Input
                  id="current-password"
                  type="password"
                  value={securityForm.current_password}
                  onChange={(e) => setSecurityForm((f) => ({ ...f, current_password: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="new-password">{t("dashboard.auth.new_password", "New Password")}</Label>
                <Input
                  id="new-password"
                  type="password"
                  value={securityForm.password}
                  onChange={(e) => setSecurityForm((f) => ({ ...f, password: e.target.value }))}
                />
              </div>
              <div className="space-y-2">
                <Label htmlFor="confirm-password">{t("dashboard.auth.confirm_password", "Confirm Password")}</Label>
                <Input
                  id="confirm-password"
                  type="password"
                  value={securityForm.password_confirmation}
                  onChange={(e) => setSecurityForm((f) => ({ ...f, password_confirmation: e.target.value }))}
                />
              </div>
              <Button type="button" disabled={saving} onClick={() => void saveSecurity()}>
                {saving ? <Loader2 className="size-4 animate-spin" /> : null}
                {t("dashboard.profile.update_password", "Update Password")}
              </Button>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>
    </div>
  );
}
