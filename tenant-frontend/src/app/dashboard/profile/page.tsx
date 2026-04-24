"use client";

import { useState } from "react";
import { Loader2, Save, Upload } from "lucide-react";
import { toast } from "sonner";
import { useAuth } from "@/context/auth-context";
import { useI18n } from "@/context/i18n-context";
import { updateProfile, uploadAvatar, changePassword } from "@/lib/tenant-resources";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";

export default function ProfilePage() {
  const { t } = useI18n();
  const { user, refreshUser } = useAuth();
  const [name, setName] = useState(user?.name ?? "");
  const [email, setEmail] = useState(user?.email ?? "");
  const [currentPassword, setCurrentPassword] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [saving, setSaving] = useState(false);

  const saveProfile = async () => {
    setSaving(true);
    try {
      await updateProfile({ name, email });
      await refreshUser();
      toast.success(t("dashboard.profile.saved", "Profile updated."));
    } catch {
      toast.error(t("dashboard.profile.save_error", "Failed to update."));
    } finally {
      setSaving(false);
    }
  };

  const savePassword = async () => {
    setSaving(true);
    try {
      await changePassword({ current_password: currentPassword, new_password: newPassword, password_confirmation: newPassword });
      setCurrentPassword(""); setNewPassword("");
      toast.success(t("dashboard.profile.password_saved", "Password changed."));
    } catch {
      toast.error(t("dashboard.profile.password_error", "Failed to change password."));
    } finally {
      setSaving(false);
    }
  };

  const handleAvatar = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0];
    if (!file) return;
    try {
      await uploadAvatar(file);
      await refreshUser();
      toast.success(t("dashboard.profile.avatar_saved", "Avatar updated."));
    } catch {
      toast.error(t("dashboard.profile.avatar_error", "Failed to upload avatar."));
    }
  };

  return (
    <div className="space-y-4">
      <div className="rounded-xl border bg-muted/40 p-4">
        <h1 className="text-xl font-semibold">{t("dashboard.profile.title", "My Profile")}</h1>
        <p className="mt-1 text-sm text-muted-foreground">{t("dashboard.profile.subtitle", "Manage your account details")}</p>
      </div>
      <Card>
        <CardHeader>
          <div className="flex items-center gap-4">
            <Avatar className="size-16">
              <AvatarImage src={(user as unknown as Record<string, unknown>)?.avatar as string ?? undefined} />
              <AvatarFallback>{user?.name?.charAt(0) ?? "?"}</AvatarFallback>
            </Avatar>
            <div>
              <CardTitle>{user?.name}</CardTitle>
              <p className="text-sm text-muted-foreground">{user?.email}</p>
              <label className="mt-2 inline-flex cursor-pointer items-center gap-1 text-sm text-primary hover:underline">
                <Upload className="size-3.5" /> {t("dashboard.profile.change_avatar", "Change avatar")}
                <input type="file" accept="image/*" className="hidden" onChange={(e) => void handleAvatar(e)} />
              </label>
            </div>
          </div>
        </CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label>{t("dashboard.auth.name", "Name")}</Label>
            <Input value={name} onChange={(e) => setName(e.target.value)} />
          </div>
          <div className="space-y-2">
            <Label>{t("dashboard.auth.email", "Email")}</Label>
            <Input type="email" value={email} onChange={(e) => setEmail(e.target.value)} />
          </div>
          <Button type="button" disabled={saving} onClick={() => void saveProfile()}>
            {saving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
            {t("dashboard.crud.save", "Save")}
          </Button>
        </CardContent>
      </Card>
      <Card>
        <CardHeader><CardTitle>{t("dashboard.profile.change_password", "Change Password")}</CardTitle></CardHeader>
        <CardContent className="space-y-4">
          <div className="space-y-2">
            <Label>{t("dashboard.profile.current_password", "Current password")}</Label>
            <Input type="password" value={currentPassword} onChange={(e) => setCurrentPassword(e.target.value)} />
          </div>
          <div className="space-y-2">
            <Label>{t("dashboard.profile.new_password", "New password")}</Label>
            <Input type="password" value={newPassword} onChange={(e) => setNewPassword(e.target.value)} />
          </div>
          <Button type="button" disabled={saving} onClick={() => void savePassword()}>
            {saving ? <Loader2 className="size-4 animate-spin" /> : <Save className="size-4" />}
            {t("dashboard.profile.change_password", "Change Password")}
          </Button>
        </CardContent>
      </Card>
    </div>
  );
}
